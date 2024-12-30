<?php

namespace App\CoreModules;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;

/**
 * Generic Object Cache
 * 
 * @template T
 */
class ObjectCached
{
    /**
     * @var array<ObjectCached>
     */
    private static array $instances = [];

    private static FilesystemAdapter $cache;

    private string $signature;

    /**
     * @var class-string<T>
     */
    private string $className;

    /**
     * @var array
     */
    private array $args;

    /**
     * @var T|null
     */
    private $object = null;

    /**
     * Constructor
     * 
     * @param class-string<T> $className
     * @param array $args
     */
    private function __construct(string $className, array $args = [])
    {
        $this->className = $className;
        $this->args = $args;
        $this->signature = $this->getsignature();
        $path = dirname($this->signature);
        if (!file_exists($path)) {
            mkdir($path);
        }
    }

    private function getsignature()
    {
        $className = $this->className;
        $argsString = json_encode($this->args);
        $signature = bin2hex((
                strlen($argsString) > 85
                ? $argsString
                : substr($argsString, 0, 85)
            )
                . $className
        );
        return $signature;
    }

    /**
     * Get the cached object
     * 
     * @return T|null
     */
    public function get(): mixed
    {
    
        if ($this->isCacheValid()) {
            if ($this->object === null) {
                // Retrieve the object from the cache
                $this->object = self::$cache->get($this->signature, function (ItemInterface $item) {
                    // Set expiration for the cached object
                    $item->expiresAfter(3600); // 1 hour expiration
                    return null; // Return null as placeholder (since we don’t want to overwrite the existing object in cache)
                });
            }
        } else {
            // Cache is not valid, create a new object
            $this->object = new $this->className(...$this->args);
        }

        return $this->object;
    }

    /**
     * Check if the cached file is still valid.
     * 
     * @return bool
     */
    private function isCacheValid(): bool
    {
        // Check cache hit
        $cachedObject = self::$cache->getItem($this->signature);
        return $cachedObject->isHit();
    }

    /**
     * Save the object to the cache.
     */
    public function save(): void
    {
        if (!$this->isCacheValid()) {
            if ($this->object === null) {
                $this->object = new $this->className(...$this->args);
            }

            $cachedObject = self::$cache->getItem($this->signature);
            $cachedObject->set($this->object);
            $cachedObject->expiresAfter(3600);
            self::$cache->save($cachedObject);
        }
    }

    /**
     * Static factory method to create an instance of ObjectCached
     * 
     * @template U
     * @param class-string<U> $class
     * @param array $args
     * @return ObjectCached<U>
     */
    public static function with(string $class, array $args = []): ObjectCached
    {
        if (!isset(self::$cache)) self::initCacheInstance();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new self($class, $args);
        }

        return self::$instances[$class];
    }

    /**
     * Save all cached objects
     */
    public static function saveAll(): void
    {
        if (!isset(self::$cache)) self::initCacheInstance();
        foreach (self::$instances as $instance) {
            $instance->save();
        }
    }

    /**
     * Initialize the cache instance.
     */
    private static function initCacheInstance()
    {
        self::$cache = new FilesystemAdapter();
    }
}
