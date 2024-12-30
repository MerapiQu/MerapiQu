<?php

namespace App\ModuleSystem\ModuleModel;

/**
 * Manifest
 * @author      ilham b <durianbohong@gmail.com>
 * @copyright   2024    ilham b
 */
class Manifest
{
    private $name;
    private $description;
    private $version;
    private $icon;
    private array $author = [];
    private string $path;
    public function __construct(string $path)
    {
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new \Exception("Error while parse manifest.json");
        }

        $reflector = new \ReflectionClass($this);
        $array = json_decode($raw, true);
        foreach ($array as $key => $value) {
            if ($reflector->hasProperty($key)) {
                $this->$key = $value;
            }
        }
        $this->path = $path;
    }

    /**
     * get module name
     * @return string|null
     */
    function getName()
    {
        return $this->name ?? basename($this->path);
    }
    /**
     * get module description
     * @return string|null
     */
    function getDescription()
    {
        return $this->description ?? null;
    }
    /**
     * get module version
     * @return string|null
     */
    function getVersion()
    {
        return $this->version ?? null;
    }
    /**
     * get module icon
     * @return string|null
     */
    function getIcon()
    {
        return $this->icon ?? null;
    }
    /**
     * get module author
     * @return array
     */
    function getAuthor()
    {
        return $this->author ?? [];
    }
    /**
     * get module path
     * @return string
     */
    function getPath()
    {
        return $this->path;
    }

    final function __debugInfo()
    {
        return [
            "name"        => $this->getName(),
            "description" => $this->getDescription(),
            "version"     => $this->getVersion(),
            "icon"        => $this->getIcon(),
            "author"      => $this->getAuthor(),
            "path"        => $this->getPath()
        ];
    }
}
