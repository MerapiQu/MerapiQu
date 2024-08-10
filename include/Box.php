<?php

namespace MerapiPanel {

    use MerapiPanel\Box\Container;
    use MerapiPanel\Box\Module\Entity\Fragment;
    use MerapiPanel\Box\Module\Entity\Module;
    use MerapiPanel\Box\Module\Entity\Proxy;
    use MerapiPanel\Box\Module\ModuleLoader;

    /**
     * Description: Box is an instance used for communication between instances in MerapiPanel, especially for modules. With a box, it allows for communication between modules.
     *
     * For more information, see the Class Box at https://github.com/MerapiPanel/MerapiPanel/wiki/Class-Box.
     *
     * @author      ilham b <durianbohong@gmail.com>
     * @copyright   Copyright (c) 2022 MerapiPanel
     * @license     https://github.com/MerapiPanel/MerapiPanel/blob/main/LICENSE
     * @lastUpdate  2024-02-10
     */

    class Box
    {

        private static $instance;
        private Container $module_container;

        protected function __construct()
        {
            $this->module_container = new Container(new ModuleLoader(__DIR__ . "/Module"));
            self::$instance = $this;
        }

        protected function initialize()
        {
            $this->module_container->initialize();
        }

        public static function module($name = null): Container|Fragment|Proxy|Module|bool
        {
            if (!$name || empty($name)) {
                return self::$instance->module_container;
            }
            return self::$instance->module_container->$name;
        }
    }
}

namespace {

    use MerapiPanel\Exception\Catcher;

    Catcher::init();

    if (!function_exists('write_log')) {
        function write_log($message, int $type = LOG_INFO)
        {
            $file = __DIR__ . "/system.log";

            // Define the log type labels
            $logTypes = [
                LOG_EMERG => 'EMERGENCY',
                LOG_ALERT => 'ALERT',
                LOG_CRIT => 'CRITICAL',
                LOG_ERR => 'ERROR',
                LOG_WARNING => 'WARNING',
                LOG_NOTICE => 'NOTICE',
                LOG_INFO => 'INFO',
                LOG_DEBUG => 'DEBUG',
            ];

            // Get the current time
            $time = date('Y-m-d H:i:s');

            // Format the log message
            $formattedMessage = sprintf("[%s] [%s] %s\n", $logTypes[$type] ?? 'UNKNOWN', $time, is_string($message) ? $message : print_r($message, 1));

            // Read the existing log file
            $logContents = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES) : [];

            // Append the new log message
            $logContents[] = $formattedMessage;

            // Ensure the log file does not exceed 3000 lines
            if (count($logContents) > 3000) {
                // Remove the oldest records to keep the log within 3000 lines
                $logContents = array_slice($logContents, -3000);
            }

            // Write the log messages back to the file
            file_put_contents($file, implode("\n", $logContents) . "\n");
        }
    }
}
