<?php

namespace Core\Exceptions\Proc;

class Params
{
    /** @var string */
    public $commandLine;

    /** @var array */
    public $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
    ];

    /** @var array */
    public $pipes = [];

    /** @var string|null */
    public $cwd = null;

    /** @var array|null */
    public $env = null;

    /** @var array|null */
    public $otherOptions = null;

    /**
     * @param array $commandParams
     */
    public function transformParamsToCommandLine(array $commandParams): void
    {
        $mapCommandLine = array_map([$this, 'escapeArgument'], $commandParams);

        $this->commandLine = 'exec ' . implode(' ', $mapCommandLine);
    }
}