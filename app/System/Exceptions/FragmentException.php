<?php

namespace App\SystemManagement\Exceptions;

use Exception;
use Symfony\Component\Filesystem\Path;
use Throwable;

class FragmentException extends BaseError
{

    function __construct(string $message, int $code = 500, Throwable $previous = null)
    {
        $basePath = Path::canonicalize(__DIR__ . "../../../../media/Module/");
        $debugs   = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        foreach ($debugs as $trace) {
            if (isset($trace["file"], $trace["line"])) {
                $file = Path::normalize($trace["file"]);
                if (strpos($file, $basePath) === 0) {
                    $this->file = $file;
                    $this->line = $trace["line"];
                    break;
                }
            }
        }

        parent::__construct($message, $code, $previous);
    }

    function getTitle(): string
    {
        return "FragmentException";
    }
}
