<?php

declare(strict_types=1);

namespace Core;

use Core\Enums\Processes\Status;
use Core\Extensions\Extension;
use Core\Processes\Process;
use Core\Processes\Processable;
use Core\Processes\TaskPacker;

class ProcessManager
{
    /** @var array */
    protected $processes = [];

    /** @var array */
    protected $inProgress = [];

    /** @var array */
    protected $finished = [];

    /** @var array */
    protected $failed = [];

    /** @var array */
    protected $timeouts = [];

    /** @var array */
    protected $results = [];

    /** @var \Core\ProcessStatistics */
    protected $statistics;

    /** @var \Core\ProcessSetting */
    protected $setting;

    /**
     * ProcessManager constructor.
     *
     * @param \Core\ProcessSetting|null $settings
     *
     * @throws \ExtensionException
     */
    public function __construct(ProcessSetting $settings = null)
    {
        if (!Extension::has(['pcntl', 'posix'])) {
            throw new \ExtensionException('Need pcntl and posix extension.');
        }

        $this->setting = $settings ?? new ProcessSetting();

        if (!file_exists($this->setting->getAutoloaderPath())) {
            throw new FileNotFoundException(
                "Autoloader file not found. Path: {$this->setting->getAutoloaderPath()}."
            );
        }

        $this->statistics = new ProcessStatistics();
        $this->registerListener();
    }

    /**
     * @param \Core\IExecutable|callable $task
     *
     * @return \Core\Processes\Processable
     * @exception FileNotFoundException
     */
    public function add($task): Processable
    {
        if (!is_callable($task) && !$task instanceof Executable) {
            throw new \InvalidArgumentException(
                'Parameter $task must be callable or instance of .' . Executable::class
            );
        }

        $process = new Process([
            $this->setting->getPathToPhp(),//'php'
            $this->fileExecutor,
            $this->setting->getAutoloaderPath(),
            TaskPacker::pack($task),
        ]);

        // Поменять на $this->processes[$process->getId()] = $process;
        // после того как проверю что все работает ключи не дублируются
        $this->processes[$process->getId()][] = $process;

        return $process;
    }

    public function getStatistics(): ProcessStatistics
    {
        $this->statistics->setInfo([
            'processes' => $this->processes,
            'finished'  => $this->finished,
            'timeout'   => $this->timeouts,
            'failed'    => $this->failed,
        ]);

        return $this->statistics;
    }

    protected function registerListener()
    {
        pcntl_async_signals(true);

        pcntl_signal(SIGCHLD, function ($signo, $status) {
            while (true) {
                $pid = pcntl_waitpid(-1, $processState, WNOHANG | WUNTRACED);

                if ($pid <= 0) {
                    break;
                }

                $process = $this->inProgress[$pid] ?? null;

                if (!$process) {
                    continue;
                }

                if ($status['status'] === 0) {
                    $this->markAsFinished($process);
                    continue;
                }

                $this->markAsFailed($process);
            }
        });
    }

    protected function markAsFinished(Process $process): void
    {
        $process->stop(Status::FINISHED);
        $this->finished[$process->getId()] = $process;
        unset($this->processes[$process->getId()]);
    }

    protected function markAsFailed(Process $process)
    {
        $process->stop(Status::FAILED);
        $this->failed[$process->getId()] = $process;
        unset($this->processes[$process->getId()]);
    }
}