<?php

namespace App\CoreModules\Blocks;

abstract class Node
{
    protected ?Node $parent = null;
    protected array $children = [];

    function getParent()
    {
        return $this->parent;
    }

    abstract function setContent($content): void;
    abstract function render(): string;
    abstract function toArray(): array;
    abstract function remove(): void;
}
