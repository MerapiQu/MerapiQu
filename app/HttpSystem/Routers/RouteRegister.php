<?php

namespace App\HttpSystem\Routers;

use Symfony\Component\Filesystem\Path;
use App\HttpSystem\Routers\Route;

final class RouteRegister
{
    /**
     * Summary of routes
     * @var array<Route> $routes
     */
    private static $routes = [];
    final private function __construct() {}

    public static function register(Route $route)
    {
       // error_log($route->getPath());
        // $path = $route->getPath();
        // $method = $route->getMethod();
        // $controller = $route->getCallback();

        // $findKey = null;
        // foreach (self::$routes as $key => $route) {
        //     if ($route->getPath() === $path && $route->getMethod() === $method && $route->getCallback() !== $controller) {
        //         $findKey = $key;
        //         break;
        //     }
        // }
        // if ($findKey != null) {
        //     self::$routes[$findKey]->addChild($route);
        // } else {
        self::$routes[] = $route;
    }


    /**
     * Summary of group
     * @param Route $route
     * @param array<Route> $children
     * @return void
     */
    public static function registerGroup(Route $route, array $children)
    {
        $parentPath = $route->getPath();
        self::register($route);
        foreach ($children as $child) {
            $childPath = Path::join($parentPath, $child->getPath());
            self::register(new Route($child->getMethod(), $childPath, $child->getCallback()));
        }
    }

    /**
     * Summary of push
     * @param \App\HttpSystem\Routers\Router $router
     * @param callable(\App\HttpSystem\Routers\Route): \App\HttpSystem\Routers\Route|null $callback A callback that receives a Route object and returns a Route object or null.
     * @return void
     */
    public static function push(Router $router, callable|array $callback = null): void
    {
        $reflector = new \ReflectionClass(Router::class);
        $routes = array_values(array_filter(array_map($callback, self::$routes)));
        if ($reflector->hasProperty("routes")) {
            $routesprop = $reflector->getProperty("routes");
            $routesprop->setAccessible(true);
            $routesprop->setValue($router, $routes);
        }
    }
}
