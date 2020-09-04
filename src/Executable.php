<?php

declare(strict_types=1);

namespace Core;

interface Executable
{
    public function __invoke();
}