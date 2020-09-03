<?php

namespace Core\Helpers;

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