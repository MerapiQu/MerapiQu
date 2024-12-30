<?php

namespace App\CoreModules\Patterns;

use Symfony\Component\Filesystem\Path;

class PatternManager
{

    private array $patterns = [];
    private array $paths = [];

    function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * render patterns
     * @param array<Pattern> $patterns
     * @return void
     */
    function render(array $patterns)
    {

        foreach ($patterns as $pattern) {
            $name = $pattern->getName();
            if (!isset($this->patterns[$name])) {
                $this->loadPattern($name);
            }
            if (!isset($this->patterns[$name])) continue;
            $item = $this->patterns[$name];
            $pattern->setContent($item->render());
        }
    }

    function loadPattern($name)
    {
        $path = $this->findPathOfPattern($name);
        if ($path) {
            $meta = new PatternProvider($path);
            $this->patterns[$name] = $meta;
            return $meta;
        }
    }

    function findPathOfPattern($name)
    {
        foreach ($this->paths as $path) {
            $path = Path::join($path, $name);
            $renderFile = Path::join($path, 'render.php');
            if (is_dir($path) && is_file($renderFile)) {
                return $path;
            }
        }
    }
}

class PatternProvider
{
    private string $path;
    public string $name;
    public array $data = [];

    function __construct(string $path)
    {
        $this->path = $path;
        $this->name = basename($path);
    }

    function render(): string
    {
        $content = file_get_contents(Path::join($this->path, 'render.php'));
        return $content;
    }
}
