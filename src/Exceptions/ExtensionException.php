<?php

declare(strict_types=1);

namespace Core\Exceptions;

class ExtensionException extends \Exception
{
    public function __construct($message = '', Throwable $previous = null)
    {
        parent::__construct($message, ExceptionCode::EXTENSION_NOT_FOUND, $previous);
    }
}