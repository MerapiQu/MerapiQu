<?php

namespace App\System;

use App\CoreModules\Database;
use App\Entity\Config;
use Doctrine\ORM\EntityRepository;
use Exception;

class Settings
{

    private static $instances = [];
    protected string $signature;

    private static Database $database;

    private function __construct(string $signature)
    {
        $this->signature = $signature;
    }

    function set($key, $value)
    {
        $signature = "{$this->signature}.{$key}";

        $entityManager = self::$database->getEntityManager();
        $config = $entityManager->find(Config::class, $signature);
        if ($config) {
            $config->setValue($value);
        } else {
            $config = new Config($signature, $value);
            $entityManager->persist($config);
        }
        $entityManager->flush();
    }

    function get($key)
    {
        $signature = "{$this->signature}.{$key}";
        $entityManager = self::$database->getEntityManager();
        $config = $entityManager->find(Config::class, $signature);
        if ($config) {
            return $config->getValue();
        }
        return null;
    }

    public static function instance()
    {

        $traces = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        if (isset($traces[1]["class"])) {
            $signature = preg_replace("/\\\\/", ".",  $traces[1]["class"]);
            $signature = strtolower($signature);
            if (!isset(self::$instances[$signature])) {
                self::$instances[$signature] = new self($signature);
            }
            return self::$instances[$signature];
        }
        throw new Exception("Instance not found!");
    }
}
