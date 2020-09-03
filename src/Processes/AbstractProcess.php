<?php

namespace Core\Processes;

abstract class AbstractProcess
{
    abstract public function start(): int;

    abstract public function stop(int $timeout): void;

    /** @var float */
    protected $startTime;

    /** @var int */
    protected $id;

    /** @var int */
    protected $pid;

    /** @var mixed */
    protected $output;

    /** @var mixed */
    protected $error;
}