<?php

namespace App\HttpSystem;

use App\Application;
use InvalidArgumentException;

class Request
{

    protected $method;
    protected URL $uri;
    protected $headers = [];
    protected $body = [];
    protected $queryParams = [];
    protected $postParams = [];
    protected $cookies = [];
    protected $files = [];

    private static $instance;

    private function __construct()
    {

        $this->method      = HTTP_METHOD::tryFrom($_SERVER['REQUEST_METHOD']);
        $this->uri         = new URL();
        $this->headers     = getallheaders();
        $this->body        = file_get_contents('php://input');
        $this->queryParams = $_GET;
        $this->postParams  = $_POST;
        $this->cookies     = $_COOKIE;
        $this->files       = $_FILES;
    }

    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    function get(string $name, $default = null)
    {
        return $this->method == HTTP_METHOD::POST
            ? ($this->postParams[$name] ?? $this->queryParams[$name] ?? $this->body[$name] ?? $default)
            : ($this->queryParams[$name] ?? $this->body[$name] ?? $default);
    }

    function getPath()
    {
        return parse_url($this->uri, PHP_URL_PATH);
    }

    public function getMethod(): ?HTTP_METHOD
    {
        return $this->method;
    }

    public function getUri(): URL
    {
        return $this->uri;
    }

    public function getHeader($name)
    {
        return $this->headers[$name] ?? null;
    }

    public function getAllHeaders()
    {
        return $this->headers;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getQueryParams()
    {
        return $this->queryParams;
    }

    public function getQueryParam($key, $default = null)
    {
        return $this->queryParams[$key] ?? $default;
    }

    public function getPostParams()
    {
        return $this->postParams;
    }

    public function getPostParam($key, $default = null)
    {
        return $this->postParams[$key] ?? $default;
    }

    public function getCookies()
    {
        return $this->cookies;
    }

    public function getCookie($name, $default = null)
    {
        return $this->cookies[$name] ?? $default;
    }

    public function getFiles()
    {
        return $this->files;
    }

    public function getFile($key)
    {
        return $this->files[$key] ?? null;
    }

    public function isMethod(HTTP_METHOD $method)
    {
        return strtoupper($this->method?->value) === strtoupper($method->value);
    }

    public function isAjax()
    {
        $keys = [
            "X-Requested-With" => "XMLHttpRequest",
            "Sec-Fetch-Mode"   => "cors"
        ];
        foreach ($keys as $key => $value) {
            if (isset($this->headers[$key]) && $this->headers[$key] === $value) {
                return true;
            }
        }
        if ($this->isAccept(HTTP_CONTENT::JSON)) {
            return true;
        }
    }

    function isAccept(HTTP_CONTENT $accept)
    {
        return in_array($accept->value, explode(",", $this->headers['Accept']??""));
    }
}
