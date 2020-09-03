<?php

declare(strict_types=1);

namespace Core\Processes;

use Core\Enums\Processes\Status;

class SimpleProcess
{
    /** @var array */
    protected $commandLine;

    /** @var int */
    protected $timeout = 60;

    /** @var bool */
    protected $isRunning = false;

    /** @var float */
    protected $startTime;

    /** @var string */
    protected $status;

    /** @var resource|false */
    protected $newProcess;

    /** @var ProcessParams */
    protected $processParams;

    public function __construct(array $commandParams)
    {
        $this->commandLine = $this->getCommandLine($commandParams);;

        $this->processParams = new ProcessParams();
        $this->processParams->setCwd(getcwd());
    }

    public function start()
    {
        $this->isRunning();
        $this->validateProcessParams();
        $this->startTime = microtime(true);

        $this->newProcess = @proc_open(
            $this->commandLine,
            $this->processParams->descriptors,
            $this->processParams->pipes,
            $this->processParams->cwd
        );

        if (!\is_resource($this->newProcess)) {
            throw new RuntimeException('Unable to launch a new process.');
        }

        $this->status = Status::STARTED;
        $this->isRunning = true;
    }

    private function getCommandLine(): string
    {
        $mapCommandLine = array_map([$this, 'escapeArgument'], $this->commandLine);

        return 'exec ' . implode(' ', $mapCommandLine);
    }

    private function isRunning(): void
    {
        if ($this->isRunning) {
            throw new RuntimeException('Process is already running.');
        }
    }

    private function validateProcessParams(): void
    {
        $cwd = $this->processParams->getCwd();

        if (!is_dir($cwd)) {
            throw new RuntimeException("The provided cwd: `{$cwd}` does not exist.");
        }
    }
}