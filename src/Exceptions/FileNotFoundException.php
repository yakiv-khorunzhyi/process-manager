<?php

declare(strict_types=1);

namespace Core\Exceptions;

class FileNotFoundException extends \Exception
{
    public function __construct($message = '', Throwable $previous = null)
    {
        parent::__construct($message, Code::FILE_NOT_FOUND, $previous);
    }
}