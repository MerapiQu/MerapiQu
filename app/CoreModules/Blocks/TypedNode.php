<?php

namespace App\CoreModules\Blocks;

class TypedNode extends Node
{

    
    public ?Node $parent;
    private string $name;
    private array $data;


    public function __construct(string $name, array $data = [])
    {
        $this->name = $name;
        $this->data = $data;
    }

    function remove(): void {}
    function render(): string
    {
        return NodeParser::fromArray([
            "attribute" => [
                "class" => ["typed-node", "typed-node_{$this->name}"]
            ],
            "children" => [
                "Unknown Type Node { $this->name }"
            ]
        ])[0]->render();
    }
    function setContent($content): void {}
    function toArray(): array
    {
        return [];
    }
}
