<?php

namespace Core;

class ProcessSetting
{
    /** @var int */
    protected $tasksPerProcess = 1;

    /** @var int */
    protected $concurrency = 30;

    /** @var int */
    protected $timeout = 300;

    /** @var int */
    protected $taskWaitTime = 50000;

    /**
     * @var string
     * @example /usr/local/php/bin/php
     */
    protected $pathToPhp = PHP_BINARY;

    /**
     * @return string
     */
    public function getPathToPhp(): string
    {
        return $this->pathToPhp;
    }

    /**
     * @param string $pathToPhp
     */
    public function setPathToPhp(string $pathToPhp): void
    {
        $this->pathToPhp = $pathToPhp;
    }

    /**
     * @return int
     */
    public function getTasksPerProcess(): int
    {
        return $this->tasksPerProcess;
    }

    /**
     * @param int $tasksPerProcess
     */
    public function setTasksPerProcess(int $tasksPerProcess): void
    {
        $this->tasksPerProcess = $tasksPerProcess;
    }

    /**
     * @return int
     */
    public function getConcurrency(): int
    {
        return $this->concurrency;
    }

    /**
     * @param int $concurrency
     */
    public function setConcurrency(int $concurrency): void
    {
        $this->concurrency = $concurrency;
    }

    /**
     * @return int
     */
    public function getTimeout(): int
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout
     */
    public function setTimeout(int $timeout): void
    {
        $this->timeout = $timeout;
    }

    /**
     * @return int
     */
    public function getTaskWaitTime(): int
    {
        return $this->taskWaitTime;
    }

    /**
     * @param int $taskWaitTime
     */
    public function setTaskWaitTime(int $taskWaitTime): void
    {
        $this->taskWaitTime = $taskWaitTime;
    }
}