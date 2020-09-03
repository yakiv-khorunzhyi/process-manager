<?php

declare(strict_types=1);

namespace Core\Processes;

class ProcessParams
{
    /** @var false|string */
    public $cwd;

    /** @var false */
    public $pty = false;

    /** @var array */
    public $descriptors = [];

    /** @var array */
    public $pipes = [];
}