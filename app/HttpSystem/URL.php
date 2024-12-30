<?php

namespace App\HttpSystem;

use App\HttpSystem\Routers\Route;
use App\Miscellaneous\Utility;

class URL
{
    private string $url;
    private ?string $query = null;
    private ?string $fragment = null;
    private string $path = "/";
    private string $host = "";
    private string $protocol = "http";

    public function __construct()
    {
        $this->url = $_SERVER['REQUEST_URI'];
        $parsed = parse_url($this->url ?? '/');
        $this->protocol = $_SERVER['HTTPS'] ?? '' === 'on' ? 'https' : 'http';
        $this->host = $_SERVER['HTTP_HOST'] ?? '';
        $this->path = $parsed['path'] ?? '/';
        $this->query = $parsed['query'] ?? null;
        $this->fragment = $parsed['fragment'] ?? null;
    }

    function getProtocol()
    {
        return $this->protocol;
    }

    function getHost()
    {
        return $this->host;
    }

    function getPath()
    {
        return $this->path;
    }

    function getQuery()
    {
        return $this->query;
    }

    function getFragment()
    {
        return $this->fragment;
    }

    function matchRoute(Route $route)
    {
        $path = $this->path;
        if (Utility::pathCompare($route->getPath(), $path)) return true;
        $path = rtrim($path, "/") . "/";
        if (!empty($route->getParameters())) {
            $pattern = preg_replace("/\//", "\/", rtrim($route->getPath(), "/") . "/");
            $pattern = "/^" . preg_replace("/\{.*?\}/m", "([^\/]+)", $pattern) . "$/";
            preg_match($pattern, $path, $matches);
            if (!empty($matches)) {
                unset($matches[0]);
                $matches = array_values($matches);
                foreach ($route->getParameters() as $key => $param) {
                    $value = $matches[$key] ?? null;
                    if ($param->hasExpacted()) {
                        if (!$param->isExpacted($value)) {
                            return false;
                        }
                    }
                    $reflector = new \ReflectionClass($param);
                    $propValue = $reflector->getProperty("value");
                    $propValue->setAccessible(true);
                    $propValue->setValue($param, $value);
                    $propValue->setAccessible(false);
                }
                if (count($matches) == count($route->getParameters())) {
                    return true;
                }
            }
        }
        return false;
    }

    function beginWith($path)
    {
        return strpos($this->path, $path) === 0;
    }

    public function __tostring()
    {
        return $this->url;
    }
}
