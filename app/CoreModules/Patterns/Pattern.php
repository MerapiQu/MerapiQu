<?php

namespace App\CoreModules\Patterns;

use App\CoreModules\Blocks\Node;
use App\CoreModules\Blocks\NodeParser;

class Pattern extends Node
{

    protected $content;
    private string $name;
    private array $data = [];


    function __construct(string $name, array $data = [])
    {
        $this->name = $name;
        $this->data = $data;
        $this->content = NodeParser::fromArray([
            "attribute" => [
                "class" => ["pattern-node", "pattern-node_{$this->name}"]
            ],
            "children" => [
                "Unknown Pattern { $this->name }"
            ]
        ])[0];
    }

    function getName(): string
    {
        return $this->name;
    }

    function remove(): void {}

    function render(): string
    {
        if ($this->content instanceof Node)
            return $this->content->render();
        return $this->content;
    }

    function setContent($content): void
    {
        $this->content = $content;
    }

    function toArray(): array
    {
        return [];
    }
}
