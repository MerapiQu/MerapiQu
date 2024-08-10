<?php

namespace MerapiPanel\Box\Module\Entity {

    use Closure;
    use Exception;
    use Symfony\Component\Filesystem\Path;
    use Throwable;

    /**
     * Description: Module Fragment Entity.
     * @author      ilham b <durianbohong@gmail.com>
     * @copyright   Copyright (c) 2022 MerapiPanel
     * @license     https://github.com/MerapiPanel/MerapiPanel/blob/main/LICENSE
     * @lastUpdate  2024-02-10
     */
    class Fragment
    {
        public readonly Fragment|Module $parent;
        public readonly string $name;
        public readonly string $path;
        protected array $childrens = [];
        protected array $listener = [];


        public function __construct(string $name, Module|Fragment $parent)
        {
            $this->name   = $name;
            $this->parent = $parent;
            $this->path   = $this->resolvePath();
        }


        public function listenOn($key, Closure $callback)
        {
            $this->listener[$key][] = $callback;
        }


        public function getModule(): Module
        {

            $fragment = $this;
            while (($fragment->parent instanceof Fragment)) {
                $fragment = $fragment->parent;
            }

            return $fragment->parent;
        }


        public function resolvePath($name = null)
        {
            $path = "";
            $fragment = $this;
            while (($fragment->parent instanceof Fragment)) {
                $path = Path::join($fragment->parent->name, $path);
                $fragment = $fragment->parent;
            }
            return Path::join($fragment->parent->path, $path, $this->name, ($name ? $name : ""));
        }



        public function resolveClassName($name = null)
        {
            $path = "";
            $fragment = $this;
            while (($fragment->parent instanceof Fragment)) {
                $path     = Path::join($fragment->parent->name, $path);
                $fragment = $fragment->parent;
            }
            return str_replace("/", "\\", Path::join($fragment->parent->namespace, $path, $this->name, ($name ? $name : "")));
        }



        /**
         * Magic method
         */
        public function __get($name)
        {

            if (empty($this->childrens[$name])) {
                $fragment = $this->getModule()->getLoader()->loadFragment($name, $this);
                $this->childrens[$name] = $fragment;
            } else {
                $fragment = $this->childrens[$name];
            }

            return $fragment;
        }


        /**
         * Magic method
         */
        public function __call($method, $args)
        {
            throw new Exception("Cannot call method $method, " . $this->resolveClassName() . " is not object");
        }


        public function __toString()
        {
            return "Fragment: {$this->resolvePath()}";
        }


        public function getContent()
        {
            try {
                if (is_file($this->path)) {
                    $content = file_get_contents($this->path);
                    if (json_validate($content)) {
                        $content = json_decode($content, true);
                    }
                    $result = &$content;
                    if (isset($this->listener["getContent"])) {
                        foreach ($this->listener["getContent"] as $callback) {
                            $callback($result, $this);
                        }
                    }
                    if (isset($this->listener["*"])) {
                        foreach ($this->listener["*"] as $callback) {
                            $callback("getContent", $result, $this);
                        }
                    }
                    return $result;
                }
            } catch (Throwable $t) {
                return null;
            }
        }


        function get($name)
        {
            return $this->__get($name);
        }
    }
}
