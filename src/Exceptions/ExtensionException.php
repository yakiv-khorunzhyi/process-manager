<?php

class ExtensionException extends \Exception
{
    private $code = 0;

    public function __construct($message = '', Throwable $previous = null)
    {
        parent::__construct($message, $this->code, $previous);
    }
}