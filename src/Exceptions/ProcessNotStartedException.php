<?php

namespace Core\Exceptions;

class ProcessNotStartedException extends \Exception
{
    public function __construct($message = '', Throwable $previous = null)
    {
        parent::__construct($message, ExceptionCode::PROCESS_NOT_STARTED, $previous);
    }
}