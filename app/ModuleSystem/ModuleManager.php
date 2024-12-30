<?php

namespace App\ModuleSystem;

use App\ModuleSystem\ModuleModel\Fragments\FragmentContext;
use App\ModuleSystem\ModuleModel\ModuleClient;
use App\ModuleSystem\ModuleModel\ModuleContext;
use App\ModuleSystem\ModuleModel\Services\ModuleService;
use App\ModuleSystem\ModuleModel\Services\ServiceContext;
use App\ModuleSystem\ModuleModel\Services\ServiceManager;
use Exception;
use Symfony\Component\Filesystem\Path;
use Throwable;


/**
 * @author      ilham b <durianbohong@gmail.com>
 * @copyright   2024    ilham b
 * @use         <Path, Exception, Throwable> 
 */
class ModuleManager
{

    private ServiceManager $serviceManager;
    /**
     * module working directory
     * @var string
     */
    readonly string $basepath;
    /**
     * project root directory
     * @var string
     */
    readonly string $root;
    /**
     * Summary of modules
     * @var array<ModuleContext> $modules
     */
    protected array $modules = [];
    /**
     * initial data 
     * this contains scaned module <name, path>
     * @var array<string, string>
     */
    protected array $initialData = [];

    protected $attributesRule = [];

    public function __construct($basepath, $root = null)
    {
        $this->root = $root ?? $this->findRoot($basepath);
        $this->basepath  = Path::canonicalize($basepath);
       
        foreach (glob(Path::join($this->basepath, "/*/"), GLOB_ONLYDIR) as $file) {
            $this->initialData[basename($file)] = $file;
        }
        $this->serviceManager = new ServiceManager([
            new DefaultService($this)
        ]);
    }


    function getModulePaths()
    {
        return array_values($this->initialData);
    }

    
    /**
     * find root by compare basepath within server path
     * @param mixed $path
     * @return string
     */
    private function findRoot($path)
    {
        $serverPath  = $_SERVER['DOCUMENT_ROOT'];
        $parts       = explode("/", Path::normalize($path));
        $serverParts = explode("/", Path::normalize($serverPath));
        $rootParts = [];
        foreach ($serverParts as $key => $part) {
            if ($part == $parts[$key]) {
                $rootParts[] = $part;
                continue;
            }
            break;
        }

        return implode("/", $rootParts);
    }
    /**
     * get root project
     * @return string
     */
    final function getRoot()
    {
        return $this->root;
    }

    function addService(ModuleService $service)
    {
        $this->serviceManager->addService($service);
    }
    function beginServices(FragmentContext $context): ServiceContext
    {
        return $this->serviceManager->load($context);
    }
    /**
     * Summary of addAttributeRule
     * @template T
     * @param class-string<T> $className The name of the class that the callable will handle.
     * @param callable(T): T $callback A callback that receives an instance of the class specified by $className and returns an instance of the same class.
     * @return void
     */
    function addAttributeRule(string $className, callable $callback): void
    {
        $this->attributesRule[$className] = $callback;
    }

    /**
     * Summary of getRules
     * @return callable[]
     */
    function getRules()
    {
        return $this->attributesRule;
    }
    /**
     * get module context
     * @param mixed $name
     * @return \App\ModuleSystem\ModuleModel\ModuleContext
     */
    final function getContext($name): ModuleContext
    {
        if (!isset($this->modules[$name])) {
            $this->load($name);
        }
        return $this->modules[$name];
    }
    /**
     * get module client
     * @param string $name
     * @return \App\ModuleSystem\ModuleModel\ModuleClient
     */
    final function get(string $name): ModuleClient
    {
        if (!isset($this->modules[$name])) {
            $this->load($name);
        }
        return $this->modules[$name]?->getClient();
    }
    /**
     * check module is exist
     * @param string $name
     * @return bool
     */
    final function has(string $name): bool
    {
        return isset($this->modules[$name]);
    }
    /**
     * unload module
     * @param string $name
     * @return void
     */
    final function remove(string $name): void
    {
        unset($this->modules[$name]);
    }
    /**
     * load spesific module
     * @param string $name
     * @throws \Exception
     * @return void
     */
    final function load(string $name): void
    {
        if (!isset($this->initialData[$name])) {
            throw new Exception("Module $name does't exist!");
        }
        $this->modules[$name] = new ModuleContext($this, $this->initialData[$name]);
    }
    /**
     * toggle method in all module 
     * @param string $method
     * @param array $args
     * @return void
     */
    final function fireAll($method, $args = []): void
    {
        foreach (array_keys($this->initialData) as $name) {
            try {
                $this->fire($name, $method, $args);
            } catch (Throwable $e) {
                // silent
                throw $e;
            }
        }
    }
    /**
     * toggle method in spesific module service
     * @param string $moduleName
     * @param string $method
     * @param array $args
     * @return mixed
     */
    final function fire($moduleName, $method, $args = []): mixed
    {
        return $this->getContext($moduleName)->getProvider()->$method(...$args);
    }
}
