<?php

namespace Core;

use Core\Processes\Processable;
use Core\Executable;

class ProcessManager
{
    /** @var \Core\ProcessSetting */
    protected $setting;

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

    /** @var bool */
    protected $stopped = false;

    /**
     * ProcessManager constructor.
     *
     * @param \Core\ProcessSetting|null $settings
     *
     * @throws \ExtensionException
     */
    public function __construct(ProcessSetting $settings = null)
    {
        if (!$this->hasRequiredExtension()) {
            throw new \ExtensionException('Need pcntl and posix extension.');
        }

        $this->setting    = $settings ?? new ProcessSetting();
        $this->statistics = new ProcessStatistics();
    }

    /**
     * @param Executable|callable $task
     * @param int|null            $outputLength
     *
     * @return \Spatie\Async\Process\Runnable
     */
    public function add($process, ?int $outputLength = null): Runnable
    {
        if (!is_callable($process) && !$process instanceof Runnable) {
            throw new InvalidArgumentException('The process passed to Pool::add should be callable.');
        }

        if (!$process instanceof Runnable) {
            $process = ParentRuntime::createProcess(
                $process,
                $outputLength,
                $this->binary
            );
        }

        $this->putInQueue($process);

        return $process;
    }

    /**
     * @param \Core\Helpers\IExecutable|callable $task
     *
     * @return \Core\Processes\Processable
     */
    public function add($task): Processable
    {
        if (!is_callable($task) && !$task instanceof Executable) {
            throw new InvalidArgumentException(
                'Parameter $task must be callable or instance of .' . Executable::class
            );
        }


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

    private function hasRequiredExtension(): bool
    {
        return extension_loaded('pcntl')
            && extension_loaded('posix');
    }
}