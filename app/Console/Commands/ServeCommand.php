<?php

namespace App\Console\Commands;

class ServeCommand
{
    public function execute()
    {
        echo "Starting PHP built-in server...\n";
        exec("php -S localhost:8000 -t public");
    }
}
