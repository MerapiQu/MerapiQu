<?php

namespace App\SystemManagement\Exceptions;

class SystemException extends BaseError
{


    function getTitle(): string
    {
        return "SystemException";
    }
}
