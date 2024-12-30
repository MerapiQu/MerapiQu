<?php

namespace App\CoreModules;

class CacheManager
{
    private string $lockFile = '/tmp/cache_manager.lock';
    private string $logFile = '/tmp/cache_manager.log';

    /**
     * Start the CacheManager as a background process.
     * If it's already running, ignore the start request.
     */
    public function start(): void
    {
        if ($this->isRunning()) {
            echo "CacheManager is already running.\n";
            return;
        }

        // Run the process in the background
        $command = sprintf(
            'php -r \'require "%s"; (new \App\CoreModules\CacheManager())->runInBackground();\' > %s 2>&1 & echo $!',
            __FILE__,
            $this->logFile
        );

        $pid = shell_exec($command);
        if ($pid) {
            file_put_contents($this->lockFile, trim($pid));
            echo "CacheManager started in the background. PID: $pid\n";
        } else {
            echo "Failed to start CacheManager.\n";
        }
    }

    /**
     * Check if the CacheManager process is already running.
     * 
     * @return bool
     */
    public function isRunning(): bool
    {
        if (!file_exists($this->lockFile)) {
            return false;
        }

        $pid = (int) file_get_contents($this->lockFile);
        return $pid > 0 && @posix_kill($pid, 0); // Check if the process is alive
    }

    /**
     * Remove the lock file.
     */
    private function removeLock(): void
    {
        if (file_exists($this->lockFile)) {
            unlink($this->lockFile);
        }
    }

    /**
     * Main background logic for the CacheManager.
     */
    public function runInBackground(): void
    {
        try {
            while (true) {
                $this->processCache();
                sleep(60); // Process cache every 60 seconds
            }
        } catch (\Exception $e) {
            file_put_contents($this->logFile, $e->getMessage() . PHP_EOL, FILE_APPEND);
        } finally {
            $this->removeLock();
        }
    }

    /**
     * Simulate cache processing logic.
     */
    private function processCache(): void
    {
        $time = date('Y-m-d H:i:s');
        echo "[$time] Processing cache in the background...\n";
        file_put_contents($this->logFile, "[$time] Cache processed.\n", FILE_APPEND);
    }
}
