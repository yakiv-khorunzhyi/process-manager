<?php

declare(strict_types=1);

namespace Core\Processes;

use Core\Enums\Processes\Status;
use Core\Exceptions\Proc\Params as ProcExtensionParams;
use Core\Exceptions\ProcessNotStartedException;

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
    protected $status = Status::READY;

    /** @var bool */
    protected $isRunning = false;

    /** @var resource|false */
    protected $instance;

    /** @var \Core\Exceptions\Proc\Params */
    protected $procExtensionParams;

    /** @var array|false */
    protected $info;

    /** @var string */
    protected $fileExecutor = __DIR__ . '/runtime.php';

    protected $output;

    public function __construct(array $commandParams)
    {
        $this->procExtensionParams = new ProcExtensionParams();
        $this->procExtensionParams->transformParamsToLine($commandParams);
    }

    public function start()
    {
        if ($this->isRunning) {
            throw new RuntimeException('Process is already running.');
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
            throw new ProcessNotStartedException('Unable to launch a new process.');
        }

        $this->isRunning = true;
        $this->info      = proc_get_status($this->process);
        $this->status    = Status::STARTED;

        $this->pid = $this->info['pid'];
        $this->id  = uniqid("{$this->pid}_", true);
    }

    public function stop($status): void
    {
        $stream          = stream_get_contents($this->procExtensionParams->pipes[1]);
        $this->output    = unserialize(base64_decode($stream));
        $this->isRunning = false;
        $this->endTime   = microtime(true);
        $this->status    = $status;

        proc_close($this->instance);
    }

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
    public function getOutput()
    {
        return $this->output;
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
     * @return \Core\Exceptions\Proc\Params
     */
    public function getProcExtensionParams(): \Core\Exceptions\Proc\Params
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