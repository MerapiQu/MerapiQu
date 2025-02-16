<?php

namespace App\Console;

class Kernel
{
    protected array $commands = [
        'migrate' => \App\Console\Commands\MigrateCommand::class,
        'serve' => \App\Console\Commands\ServeCommand::class,
    ];

    public function handle(array $argv)
    {
        $commandName = $argv[1] ?? 'help';

        if (isset($this->commands[$commandName])) {
            $command = new $this->commands[$commandName]();
            $command->execute();
        } else {
            echo "Command not found: $commandName\n";
            $this->showHelp();
        }
    }

    protected function showHelp()
    {
        echo "Available commands:\n";
        foreach (array_keys($this->commands) as $command) {
            echo "  - $command\n";
        }
    }
}
