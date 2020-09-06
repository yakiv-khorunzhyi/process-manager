<?php

declare(strict_types=1);

namespace Core\Processes;

class Status
{
    const NOT_STARTED = 'not started';

    const RUNNING = 'running';

    const FINISHED = 'finished';

    const FAILED = 'failed';

    const TERMINATED = 'terminated';
}