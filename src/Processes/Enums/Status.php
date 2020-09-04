<?php

declare(strict_types=1);

namespace Core\Enums\Processes;

class Status
{
    const READY = 'ready';

    const STARTED = 'started';

    const FINISHED = 'finished';

    const FAILED = 'failed';

    const TERMINATED = 'terminated';
}