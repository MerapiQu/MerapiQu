<?php

namespace App\HttpSystem;

use App\System\Views\PageDocument;
use App\System\Views\ViewDocument;
use InvalidArgumentException;

class Response
{

    public function __construct($content = "", $code = HTTP_CODE::OK, array $headers = [])
    {
        $this->content = $content;
        $this->code    = $code;
        $this->headers = $headers;
    }


    protected mixed $content = "";
    final public function setContent($content)
    {
        $this->content = $content;
    }

    final public function getContent()
    {
        return $this->content;
    }

    protected $headers = [];

    final public function getHeaders()
    {
        return $this->headers;
    }

    protected HTTP_CODE $code = HTTP_CODE::OK;

    final public function setCode(HTTP_CODE|int $code)
    {
        if (is_integer($code)) {
            $code = HTTP_CODE::fromCode($code);
        }

        $this->code = $code;
        return $this;
    }

    final public function getCode()
    {
        return $this->code;
    }



    protected $content_type = "text/html";

    final public function setContentType($contentType)
    {
        $this->content_type = $contentType;
        return $this;
    }

    protected $content_encoding = "utf-8";

    final public function setEncoding($encoding)
    {
        $this->content_encoding = $encoding;
        return $this;
    }

    private array $cookies = [];

    /**
     * Sets a cookie with the specified name, value, and options.
     *
     * @param mixed $name The name of the cookie.
     * @param mixed $value The value of the cookie.
     * @param array $options An associative array of options to customize the cookie:
     *                       - expire (int): The expiration time of the cookie (Unix timestamp).
     *                       - path (string): The path where the cookie is available (default is '/').
     *                       - domain (string): The domain where the cookie is available.
     *                       - secure (bool): Whether the cookie should only be transmitted over HTTPS (default is false).
     *                       - httponly (bool): Whether the cookie is accessible only via HTTP (default is false).
     *
     * @return void
     */
    function setCookie($name, $value, array $options = [])
    {

        if (empty($name)) throw new InvalidArgumentException("Cookie name cannot be empty.");
        if (empty($value)) throw new InvalidArgumentException("Cookie value cannot be empty.");

        // Set default expiration to 0 (session cookie) if not provided
        $expire = isset($options['expire']) ? $options['expire'] : 0;

        // Ensure other options are correctly passed
        $path = isset($options['path']) ? $options['path'] : '/';
        $domain = isset($options['domain']) ? $options['domain'] : '';
        $secure = isset($options['secure']) ? $options['secure'] : false;
        $httponly = isset($options['httponly']) ? $options['httponly'] : false;
        $this->cookies[$name] = [
            "value" => $value,
            "expire" => $expire,
            "path" => $path,
            "domain" => $domain,
            "secure" => $secure,
            "httponly" => $httponly
        ];
    }


    final function http_response_code(HTTP_CODE $code): void
    {

        if (!function_exists('http_response_code')) {
            $protocol = $_SERVER['SERVER_PROTOCOL'] ?? 'HTTP/2.0';
            header($protocol . ' ' . $code->value . ' ' . $code->reasonPhrase());
        } else {
            @http_response_code($code->value);
        }
    }

    final public function send(): string
    {
        $content = $this->content;
        $this->http_response_code($this->code);

        // Handle rendering for specific content types
        if (($this->content instanceof ViewDocument) || $this->content instanceof PageDocument) {
            ob_start();
            $this->content_type = "text/html";
            $this->content_encoding = "utf-8";
            echo $this->content->render();
            $content = ob_get_contents();
            ob_end_clean();
        }
        
        $content = is_string($content) ? $content : json_encode($content);
        // Calculate Content-Length correctly
        $this->headers["content-length"] = mb_strlen($content, '8bit');
        $this->headers["content-type"] = $this->content_type;
        $this->headers["content-encoding"] = $this->content_encoding;

        // Ensure no extra output corrupts headers
        if (ob_get_length() > 0) {
            ob_clean();
        }

        // Send headers
        foreach ($this->headers as $key => $value) {
            header("{$key}: {$value}");
        }

        // Set cookies
        foreach ($this->cookies as $name => $cookie) {
            setcookie(
                $name,
                $cookie['value'],
                $cookie['expire'],
                $cookie['path'],
                $cookie['domain'],
                $cookie['secure'],
                $cookie['httponly']
            );
        }

        // Return the content as a string or JSON-encoded
        return $content;
    }


    public static function redirect(string $path)
    {
        return new self([], HTTP_CODE::TEMPORARY_REDIRECT, [
            "Location" => "/" . ltrim($path, "/")
        ]);
    }
}
