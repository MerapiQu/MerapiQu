<?php

namespace App\ModuleSystem\ModuleModel\Fragments;

use App\HttpSystem\Controller\Callback;
use App\HttpSystem\Map\DELETE;
use App\HttpSystem\Map\GET;
use App\HttpSystem\Map\POST;
use App\HttpSystem\Map\PUT;
use App\HttpSystem\Routers\Route;
use App\HttpSystem\Routers\Router;
use App\ModuleSystem\ModuleManager;
use App\ModuleSystem\ModuleModel\ModuleContext;
use BadMethodCallException;
use Exception;
use LogicException;
use ReflectionAttribute;
use ReflectionObject;
use Symfony\Component\Filesystem\Path;

/**
 * FragmentContext
 * @description context control and splited what a methods able void by public and not 
 * @author      ilham b <durianbohong@gmail.com>
 * @copyright   2024    ilham b
 * 
 */
class FragmentContext extends WireTransformer
{
    readonly string $path;
    readonly string $signature;
    readonly ModuleManager $manager;

    protected Fragment $provider;
    protected FragmentClient $client;
    readonly FragmentContext|null $parent;
    /**
     * @var array<FragmentContext> $children
     */
    protected array $children = [];
    //protected array $blockedMethods = [];
    /**
     * @var array<FragmentMethod>
     */
    protected array $methods = [];

    public function __construct(ModuleManager $manager, string $path, ?FragmentContext $parent = null)
    {
        $this->parent    = $parent;
        $this->manager   = $manager;
        $this->path      = $path;
        $this->signature = bin2hex($this->path);
        $this->provider  = $this->initProvider();
        $this->client    = new FragmentClient($this->signature);

        $this->injectProperty("signature", $this->signature);
        $this->syncMethods();

        parent::__construct();
    }


    #[\Override]
    function call(string $classOrigin, string $method, array $args = [])
    {

        $serviceContext = $this->manager->beginServices($this);
        $fragmentMethod =  $serviceContext->handle($classOrigin, $method, function ($classOrigin, $method) {
            if (array_key_exists($method, $this->methods)) {
                $method = $this->methods[$method];
                $isPublic = $method->isPublic();
                if ($classOrigin == FragmentClient::class && !$isPublic) {
                    throw new Exception("Method \"{$method->getName()}\" is not accesible from public");
                }
                return $method;
            }
            throw new Exception("Method \"$method\" not found");
        });

        $reflector = new ReflectionObject($fragmentMethod);
        $methodprop = $reflector->getProperty("reflectionMethod");
        $methodprop->setAccessible(true);
        /**
         * @var \ReflectionMethod
         */
        $reflectionprop = $methodprop->getValue($fragmentMethod);

        $providerprop = $reflector->getProperty("provider");
        $providerprop->setAccessible(true);
        /**
         * @var object
         */
        $provider = $providerprop->getValue($fragmentMethod);

        // fire services onCall
        $event = new MethodEvent($classOrigin, $method, $args, $fragmentMethod);
        $serviceContext->fireOnCall($event);

        ob_start();
        $output = $reflectionprop->invokeArgs($provider, $args);
        ob_end_clean();
        return $output;
    }


    #[\Override]
    function getSignature(): string
    {
        return $this->signature;
    }


    protected function initProvider(): ?Fragment
    {
        $className = $this->getClassName();
        if (class_exists($className)) {
            $reflector = new \ReflectionClass($className);
            $fragment = $reflector->newInstanceWithoutConstructor();
            if ($fragment instanceof Fragment) {
                $fragment->onCreate();
                return $fragment;
            }
            throw new Exception("Module Components should be instance of Fragment", 500);
        }
    }

    protected function getAttributeRule($attributeClass, $methodName)
    {
        $reflector = new \ReflectionClass($this->getClassName());
        if ($reflector->hasMethod($methodName)) {
            $reflectionMethod = $reflector->getMethod($methodName);
            $attributes = $reflectionMethod->getAttributes($attributeClass);
            if (count($attributes) > 0)
                return $attributes[0]->newInstance();
        }
    }

    protected function injectProperty(string $name, $value): void
    {
        $reflector = new ReflectionObject($this->provider);
        while (!$reflector->hasProperty($name) && $reflector->getParentClass()) {
            $reflector = $reflector->getParentClass();
        }

        $property = $reflector->getProperty($name);
        $property->setAccessible(true);
        $property->setValue($this->provider, $value);
        $property->setAccessible(false);
    }

    protected function syncMethods(): void
    {
        $reflector = new ReflectionObject($this->provider);
        foreach ($reflector->getMethods() as $reflectionMethod) {
            $attributes = $reflectionMethod->getAttributes();
            if (!empty($attributes)) $this->processMethodAttribute(
                $reflectionMethod,
                $attributes
            );
            $this->methods[$reflectionMethod->getName()] = new FragmentMethod(
                $this->provider,
                $reflectionMethod
            );
        }
    }

    protected function processMethodAttribute($reflectionMethod, $attributes)
    {
        $httpMethods = [
            GET::class => 'GET',
            POST::class => 'POST',
            PUT::class => 'PUT',
            DELETE::class => 'DELETE',
        ];

        $foundHttpMethod = null; // Tracks the first HTTP method found.

        foreach ($attributes as $attribute) {
            if ($attribute instanceof ReflectionAttribute) {
                $attributeClass = $attribute->getName();

                if (array_key_exists($attributeClass, $httpMethods)) {
                    // Check if an HTTP method was already assigned
                    if ($foundHttpMethod !== null) {
                        throw new LogicException(
                            sprintf(
                                'Conflicting HTTP methods ("%s" and "%s") found on method "%s". Only one HTTP method (GET, POST, PUT, DELETE) is allowed per method.',
                                $httpMethods[$foundHttpMethod],
                                $httpMethods[$attributeClass],
                                $reflectionMethod->getName()
                            )
                        );
                    }

                    // Mark the current HTTP method as found
                    $foundHttpMethod = $attributeClass;

                    // Retrieve attribute arguments
                    $arguments = $attribute->getArguments();
                    $path = $arguments['path'] ?? null;

                    if ($path) {
                        Router::add(Route::{$httpMethods[$attributeClass]}(
                            $path,
                            Callback::from($this->provider, $reflectionMethod->getName())
                        ));
                    }
                }
            }
        }
    }

    protected function getClassName(): string
    {
        $path = $this->getPath();
        if (is_dir($path)) {
            if (is_file(Path::join($path, "Service.php"))) {
                $path = Path::join($path, "Service");
            }
        }
        $relativePath = Path::makeRelative($path, $this->manager->getRoot());
        $splited      = explode("/", preg_replace("/\\|\/|\\\\|\/\//m", "/", $relativePath));
        $className    =  "\\" . implode("\\", array_map("ucfirst", $splited));
        if (!class_exists($className) && file_exists($path . ".php")) {
            include_once $path . ".php";
        }
        return $className;
    }

    protected function load($name): void
    {
        $path = $this->getPath();
        if (is_file($path . ".php")) {
            $path  = Path::canonicalize($path . "/..");
        }
        $path = Path::join($path, $name);
        $this->children[$name] = new self($this->manager, $path, $this);
    }

    public function getPath(): string
    {
        return $this->path;
    }


    public function getModule()
    {
        $context = $this;
        while (!($context instanceof ModuleContext) && $context != null) {
            $context = $context->getParent();
        }
        return $context;
    }


    public function getClient($name = null): FragmentClient
    {
        if (is_string($name) && !empty($name)) {
            if (!isset($this->children[$name])) {
                $this->load($name);
            }
            return $this->children[$name]->getClient();
        }
        return $this->client;
    }


    public function getProvider($name = null): Fragment
    {
        if (is_string($name) && !empty($name)) {
            if (!isset($this->children[$name])) {
                $this->load($name);
            }
            return $this->children[$name]->getProvider();
        }
        return $this->provider;
    }


    public function getParent()
    {
        return $this->parent;
    }
}
