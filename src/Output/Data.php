<?php

declare(strict_types=1);

namespace Core\Output;

class Data
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function get()
    {
        return $this->data;
    }
}