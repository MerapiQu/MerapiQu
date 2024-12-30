<?php

namespace App\Miscellaneous;

use InvalidArgumentException;
use Symfony\Component\Filesystem\Path;

class Utility
{

    static function normalizePath($path)
    {
        return preg_replace("/\\|\/|\\\\|\\\\\\|\/\//m", "/", $path);
    }

    static function getcwd($path = "/")
    {
        return Path::join(Path::normalize(__DIR__ . "/../../"), $path);
    }
    static function pathCompare($path1, $path2)
    {
        $path1 = trim($path1, "/");
        $path2 = trim($path2, "/");
        return ($path1 == $path2);
    }

    public static function random($length = 10, $chars = "a-zA-Z0-9!@#$%^&*()_")
    {
        // Generate the character pool based on the input pattern
        $charPool = '';
        if (strpos($chars, 'a-z') !== false) {
            $charPool .= implode('', range('a', 'z'));
        }
        if (strpos($chars, 'A-Z') !== false) {
            $charPool .= implode('', range('A', 'Z'));
        }
        if (strpos($chars, '0-9') !== false) {
            $charPool .= implode('', range('0', '9'));
        }
        $specialChars = preg_replace('/[a-zA-Z0-9-]/', '', $chars); // Extract special characters
        $charPool .= $specialChars;

        // Ensure the pool is not empty
        if (empty($charPool)) return "";


        // Generate the random string
        $randomString = '';
        $poolLength = strlen($charPool);
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $charPool[random_int(0, $poolLength - 1)];
        }

        return $randomString;
    }
}
