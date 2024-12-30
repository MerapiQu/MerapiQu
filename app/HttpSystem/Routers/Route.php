<?php

namespace App\HttpSystem\Routers;

use App\HttpSystem\Controller\Callback;
use App\HttpSystem\HTTP_METHOD;
use App\HttpSystem\Map\DELETE;
use App\HttpSystem\Map\GET;
use App\HttpSystem\Map\POST;
use App\HttpSystem\Map\PUT;
use App\HttpSystem\Middlewares\Middleware;
use LogicException;
use ReflectionAttribute;
use ReflectionMethod;

class Route
{

    private string $path;
    private readonly HTTP_METHOD $method;
    private readonly Callback $callback;
    private readonly array $parameters;
    /**
     * @var array<Middleware> $middlewares
     */
    private array $middlewares = [];
    /**
     * Summary of children chains
     * @var array<Callback> $children
     */
    public array $children = [];


    /**
     * Route constructor
     * @param HTTP_METHOD $method
     * @param string $path
     * @param Callback $callback
     * @param array<Middleware> $middlewares
     */
    public function __construct(HTTP_METHOD $method, string $path, Callback $callback, array $middlewares = [])
    {

        $this->method      = $method;
        $this->path        = "/" . ltrim($path, "/");
        $this->callback    = $callback;
        $this->middlewares = $middlewares;

        $parameters = [];
        preg_match_all("/(\{(.*?)\})/", $path, $mathes);
        if (isset($mathes[2])) {
            foreach ($mathes[2] as $math) {
                $expacted = [];
                preg_match("/(\w+)(\[(.*?)\])/", $math, $mathes);
                if (isset($mathes[1], $mathes[3])) {
                    $name     = $mathes[1];
                    $expacted = explode(",", $mathes[3]);
                } else {
                    $name = $math;
                }
                $parameters[] = new RouteParam($name, $expacted);
            }
        }
        $this->parameters = $parameters;
    }

    function getMethod(): HTTP_METHOD
    {
        return $this->method;
    }

    function getPath(): string
    {
        return $this->path;
    }

    function setPath(string $path): void
    {
        $this->path = $path;
    }


    function getCallback(): Callback
    {
        return $this->callback;
    }

    function getParameters(): array
    {
        return $this->parameters ?? [];
    }

    /**
     * getMiddleware
     * @return array<Middleware>
     */
    function getMiddleware(): array
    {
        return $this->middlewares;
    }

    function addMiddleWare(Middleware $middleware): void
    {
        $this->middlewares[] = $middleware;
    }

    function addChild(Callback $callback): void
    {
        $this->children[] = $callback;
    }

    function __debugInfo()
    {
        return [
            "method"   => $this->method->value,
            "path"     => $this->path,
            "callback" => $this->callback,
            "parameters" => $this->parameters,
            "middlewares" => $this->middlewares,
            "children" => $this->children
        ];
    }

    static function GET(string $path, Callback $callback): Route
    {
        return new self(HTTP_METHOD::GET, $path, $callback);
    }
    static function POST(string $path, Callback $callback): Route
    {
        return new self(HTTP_METHOD::POST, $path, $callback);
    }
    static function PUT(string $path, Callback $callback): Route
    {
        return new self(HTTP_METHOD::PUT, $path, $callback);
    }
    static function DELETE(string $path, Callback $callback): Route
    {
        return new self(HTTP_METHOD::DELETE, $path, $callback);
    }

    static function fromController(object $controller)
    {
        $routes = [];
        $reflector = new \ReflectionClass($controller);
        $methods = $reflector->getMethods();
        foreach ($methods as $method) {
            $attributes = $method->getAttributes();
            if (!empty($attributes)) {
                $route = Route::getRouteFromMethod($method, $attributes, $controller);
                if ($route) {
                    $routes[] = $route;
                }
            } else {
                error_log("Skip $method {${get_class($controller)}}");
            }
        }

        return $routes;
    }

    /**
     * Summary of getRouteFromMethod
     * @param \ReflectionMethod $reflectionMethod
     * @param mixed $attributes
     * @param object $provider
     * @throws \LogicException
     * @return \App\HttpSystem\Routers\Route|null
     */
    static function getRouteFromMethod(ReflectionMethod $reflectionMethod, $attributes, object $provider): Route|null
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
                    $path = null;
                    // Retrieve attribute arguments
                    $arguments = $attribute->getArguments();
                    if (array_is_list($arguments)) {
                        $path = $arguments[0] ?? null;
                    } else if (is_array($arguments)) {
                        $path = $arguments['path'] ?? null;
                    }

                    if ($path) {
                        return Route::{$httpMethods[$attributeClass]}(
                            $path,
                            Callback::from(
                                $provider,
                                $reflectionMethod->getName()
                            )
                        );
                    }
                }
            }
        }
        return null;
    }
}


class RouteParam
{
    public readonly string $name;
    private readonly array $expacted;
    private string|null $value = null;
    public function __construct(string $name, array $expacted = [])
    {
        $this->name  = $name;
        $this->expacted = $expacted;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValue(): string|null
    {
        return $this->value;
    }

    function hasExpacted(): bool
    {
        return count($this->expacted) > 0;
    }

    function isExpacted($value): bool
    {
        return in_array($value, $this->expacted);
    }
}
