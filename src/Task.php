<?php

declare(strict_types=1);

namespace Core\Helpers;

use Core\Executable;

class Task
{
    /** @var callable|null */
    protected $function;

    public function __construct(callable $function)
    {
        $this->function = $function;
    }

    public function get(): callable
    {
        return $this->function;
    }
}