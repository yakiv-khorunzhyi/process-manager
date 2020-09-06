<?php

declare(strict_types=1);

namespace Core;

use Core\Processes\Process;

class ProcessStorage
{
    /** @var array */
    public $waiting = [];

    /** @var array */
    public $inProgress = [];

    /** @var array */
    public $finished = [];

    /** @var array */
    public $failed = [];

    /** @var array */
    public $terminated = [];

    public function addToFinished(Process $process): void
    {
        unset($this->inProgress[$process->getId()]);
        $this->finished[$process->getId()] = $process;
    }

    public function addToFailed(Process $process)
    {
        unset($this->inProgress[$process->getId()]);
        $this->failed[$process->getId()] = $process;
    }
    public function addToTerminated(Process $process)
    {
        unset($this->inProgress[$process->getId()]);
        $this->terminated[$process->getId()] = $process;
    }

}