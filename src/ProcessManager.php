<?php

declare(strict_types=1);

namespace Core;

use Core\Processes\Process;
use Core\Processes\Processable;
use Core\Processes\Serializator;

class ProcessManager
{
    protected $storage;


    /** @var string */
    protected $fileExecutor = __DIR__ . '/Processes/Runtime/runtime.php';


    /** @var \Core\ProcessManagerStatistics */
    protected $statistics;

    /** @var \Core\ProcessManagerSetting */
    protected $setting;


    /**
     * ProcessManager constructor.
     *
     * @param \Core\ProcessManagerSetting|null $settings
     *
     * @throws \ExtensionException
     */
    public function __construct(\Core\ProcessManagerSetting $settings = null)
    {
        if (!\Core\Extensions\Extension::has(['pcntl', 'posix'])) {
            throw new \ExtensionException('Need pcntl and posix extension.');
        }

        $this->setting = $settings ?? new ProcessManagerSetting();

        if (!file_exists($this->setting->getAutoloaderPath())) {
            throw new FileNotFoundException(
                "Autoloader file not found. Path: {$this->setting->getAutoloaderPath()}."
            );
        }

        $this->storage = new ProcessStorage();
        $this->statistics = new ProcessManagerStatistics();
        $this->registerListener();
    }



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
            Serializator::serialize($task),
        ]);

        $process->bindStorage($this->storage);

        return $process;
    }

    public function getStatistics(): ProcessManagerStatistics
    {
        $this->statistics->setInfo([
            'processes' => $this->waiting,
            'finished' => $this->finished,
            'timeout' => $this->timeouts,
            'failed' => $this->failed,
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
        $process->finish(\Core\Processes\Status::FINISHED);
        $this->finished[$process->getId()] = $process;
        unset($this->waiting[$process->getId()]);
    }

    protected function markAsFailed(Process $process)
    {
        $process->finish(\Core\Processes\Status::FAILED);
        $this->failed[$process->getId()] = $process;
        unset($this->waiting[$process->getId()]);
    }
}