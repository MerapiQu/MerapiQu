<?php

namespace App\System\Exceptions;

use Throwable;

abstract class BaseError extends \Exception
{
    public function __construct($message, $code = 0,  Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
    abstract function getTitle(): string;
}
