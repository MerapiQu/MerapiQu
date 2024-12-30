<?php

namespace App\CoreModules\Blocks;

const ESCAPE_TWIG = 1;
const ESCAPE_HTML = 2;

class TextNode extends Node
{
    protected $text;
    protected $flags;

    public function __construct($text, $flags = ESCAPE_TWIG | ESCAPE_HTML)
    {
        $this->text = $text;
        $this->flags = $flags;
    }

    public function setContent($content): void
    {
        $this->text = $content;
    }

    function remove(): void
    {
        if ($this->parent) {
            $this->parent->children = array_diff($this->parent->children, [$this]);
        }
    }

    public function render(): string
    {
        $text = $this->text;
        return $text;
    }

    public function toArray(): array
    {
        return [
            "type" => "textnode",
            "children" => $this->text
        ];
    }

    function __debugInfo()
    {
        return [
            "type" => "textnode",
            "text" => $this->text
        ];
    }

    public function __tostring()
    {
        return $this->render();
    }
}
