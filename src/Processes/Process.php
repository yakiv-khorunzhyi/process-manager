<?php

declare(strict_types=1);

namespace Core\Processes;

use Core\ProcessStorage;

class Process
{
    /** @var int */
    protected $id;

    /** @var int */
    protected $pid;

    /** @var float */
    protected $startTime;

    /** @var float */
    protected $endTime;

    /** @var string */
    protected $status = \Core\Processes\Status::NOT_STARTED;


    /** @var bool */
    protected $isRunning = false;


    /** @var resource|false */
    protected $instance;

    protected $successOutput;

    protected $failOutput;


    /** @var \Core\Exceptions\Proc\Params */
    protected $procExtensionParams;

    /** @var array|false */
    protected $info;

    /** @var \Core\ProcessStorage */
    protected $storage;


    /** @var int */
    protected static $currentProcessId = 0;


    public function __construct(array $commandParams)
    {
        $this->procExtensionParams = new \Core\Exceptions\Proc\Params();
        $this->procExtensionParams->transformParamsToCommandLine($commandParams);
    }


    public function start()
    {
        if ($this->isRunning) {
            throw new \RuntimeException('Process is already running.');
        }

        $this->startTime = microtime(true);

        $this->instance = @proc_open(
            $this->procExtensionParams->commandLine,
            $this->procExtensionParams->descriptors,
            $this->procExtensionParams->pipes,
            $this->procExtensionParams->cwd,
            $this->procExtensionParams->env,
            $this->procExtensionParams->otherOptions
        );

        if (!\is_resource($this->instance)) {
            throw new \Core\Exceptions\ProcessNotStartedException(
                'Unable to launch a new process.'
            );
        }

        $this->info = proc_get_status($this->process);
        $this->status = Status::RUNNING;

        $this->pid = $this->info['pid'];
        $this->id = self::$currentProcessId . "_{$this->pid}";

        ++self::$currentProcessId;
        if (self::$currentProcessId === PHP_INT_MAX) {
            self::$currentProcessId = 0;
        }

        $this->storage->inProgress[$this->id] = $this;
        $this->isRunning = true;
    }

    public function terminate(): void
    {
        $this->stop(Status::TERMINATED);
        @proc_terminate($this->process);

        $this->storage->addToTerminated($this);
    }

    public function finish(): void
    {
        $this->stop(Status::FINISHED);
        @proc_close($this->instance);

        $this->storage->addToFinished($this);
    }

    private function stop($status)
    {
        $stream = stream_get_contents($this->procExtensionParams->pipes[1]);
        $output = unserialize(base64_decode($stream));

        if ($output instanceof FailOutput) {
            $this->failOutput = $output;
        } else {
            $this->successOutput = $output;
        }

        $this->endTime = microtime(true);
        $this->status = $status;

        $this->isRunning = false;
    }

    public function bindStorage(ProcessStorage $storage): void
    {
        $this->storage = $storage;
    }

    /**
     * ---===### Getters ###===---
     */

    /**
     * @return float
     */
    public function getEndTime(): float
    {
        return $this->endTime;
    }

    /**
     * @return mixed
     */
    public function getSuccessOutput()
    {
        return $this->successOutput;
    }

    /**
     * @return mixed
     */
    public function getFailOutput()
    {
        return $this->failOutput;
    }

    /**
     * @return false|resource
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * @return float
     */
    public function getStartTime(): float
    {
        return $this->startTime;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return array|false
     */
    public function getInfo()
    {
        return $this->info;
    }

    /**
     * @return ProcExtensionParams
     */
    public function getProcExtensionParams(): ProcExtensionParams
    {
        return $this->procExtensionParams;
    }

    /**
     * @return bool
     */
    public function isRunning(): bool
    {
        return $this->isRunning;
    }
}