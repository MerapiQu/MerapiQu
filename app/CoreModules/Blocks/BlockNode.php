<?php

namespace App\CoreModules\Blocks;

use App\CoreModules\Blocks\Node;

interface IBlockNode
{
    /**
     * add className to this node
     * @param string $class
     * @return void
     */
    function addClass(string $class);

    /**
     * remove className to this node
     * @param string $class
     * @return void
     */
    function removeClass(string $class);

    /**
     * check className is exist in this node
     * @param string $class
     * @return void
     */
    function hasClass(string $class);

    /**
     * get classList
     * @return array
     */
    function getClasses(): array;

    /**
     * append another node
     * @param array|string|Node $node
     * @return void
     */
    function append(array|string|Node $node);

    /**
     * prepend another node
     * @param array|string|Node $node
     * @return void
     */
    function prepend(array|string|Node $node);

    /**
     * get node children
     * @return array
     */
    function getChildren(): array;

    /**
     * add node attribute
     * @param string $attribute
     * @param mixed $value
     * @return void
     */
    function addAttr(string $attribute, $value): void;

    /**
     * set node attribute
     * @param array $attribute
     * @return void
     */

    /**
     * check attribute is exist in this node
     * @param string $attribute
     * @return bool
     */
    function hasAttr(string $attribute): bool;

    /**
     * get attribute value
     * @param string $attribute
     * @return string
     */
    function getAttr(string $attribute): string;

    /**
     * remove attribute
     * @param string $attribute
     * @return void
     */
    function removeAttr(string $attribute): void;

    /**
     * combine attribute with another attribute
     * @param array $attribute
     * @return void
     */
    function appendAttribute(array $attribute): void;

    /**
     * set attribute replace old attribute
     * @param array $attribute
     * @return void
     */
    function setAttribute(array $attribute): void;

    /**
     * transform to array
     * @return array
     */
    function toArray(): array;

    /**
     * check node has children
     * @return bool
     */
    function hasChildren(): bool;
}



class BlockNode extends Node implements IBlockNode
{

    readonly string $tagName;
    /**
     * @var array<BlockNode> $children
     */

    public array $attribute = [];
    public array $classes = [];
    readonly bool $selfClose;
    /**
     * cached queries
     * @var array
     */
    private array $queries = [];

    public function __construct(string $tagName = "div", string|array|null $children = null,  array $attribute = null)
    {
        $this->tagName    = strtolower(trim($tagName));
        if (in_array($this->tagName, ['area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'link', 'meta', 'param', 'source', 'track', 'wbr',])) {
            $this->selfClose = true;
        } else {
            $this->selfClose = false;
        }
        $this->setContent($children);
        if (is_array($attribute) && !array_is_list($attribute)) {
            $this->setAttribute($attribute);
        }
    }

    function getTag(): string
    {
        return $this->tagName;
    }

    function hasChildren(): bool
    {
        return $this->selfClose ? false : count($this->getChildren());
    }

    function getChildren(): array
    {
        return $this->children;
    }

    function setAttribute(array $attribute): void
    {
        if (isset($attribute['class'])) {
            $classes = is_string($attribute['class']) ? explode(' ', $attribute['class']) : $attribute['class'];
            $this->setClasses($classes);
            unset($attribute['class']);
        }
        $this->attribute = $attribute;
    }

    function appendAttribute(array $attribute): void
    {
        $this->attribute = array_merge($this->attribute, $attribute);
    }

    function addAttr(string $attribute, $value): void
    {
        $this->attribute[$attribute] = $value;
    }
    function removeAttr(string $attribute): void
    {
        unset($this->attribute[$attribute]);
    }
    function hasAttr(string $attribute): bool
    {
        return isset($this->attribute[$attribute]);
    }
    function getAttr(string $attribute): string
    {
        return $this->attribute[$attribute] ?? '';
    }

    function getClasses(): array
    {
        return $this->classes;
    }

    function hasClass(string $class): bool
    {
        return isset($this->classes[$class]);
    }

    function append(mixed $node): void
    {
        if ($node instanceof Node) {
            $this->children[] = $node;
        } else if (is_array($node)) {

            $this->children = array_merge(
                $this->children,
                NodeParser::fromArray(
                    $node
                )
            );
        } else if (is_string($node)) {
            if (NodeParser::isHTML($node)) {
                $this->children = array_merge($this->children, NodeParser::fromHTML($node));
            } else {
                $this->children = array_merge($this->children, [new TextNode($node)]);
            }
        }
        foreach ($this->children as $component) {
            $component->parent = $this;
        }
    }

    function prepend(mixed $node): void
    {
        if ($node instanceof Node) {
            array_unshift($this->children, $node);
        } else if (is_array($node)) {
            $this->children = array_merge(NodeParser::fromArray($node), $this->children);
        }
        foreach ($this->children as $component) {
            $component->parent = $this;
        }
    }

    function addClass(string $class): void
    {
        $this->classes[] = $class;
        $this->classes = array_unique($this->classes);
    }

    function removeClass(string $class): void
    {
        $this->classes = array_diff($this->classes, [$class]);
    }

    function setClasses(array $classes): void
    {
        $this->classes = array_unique(array_merge($this->classes, $classes));
    }

    function toArray(): array
    {
        $arrayBlock = [
            "tagName" => $this->tagName,
            "attribute" => $this->attribute,
            "classes" => $this->classes,
            "children" =>  array_map(fn($b) => $b->toArray(), $this->children)
        ];
        $arrayBlock['childCount'] = count($this->children);
        $arrayBlock = array_filter($arrayBlock, fn($v) => !empty($v));

        return $arrayBlock;
    }

    function setContent($content): void
    {
        $this->children = [];
        $this->append($content);
    }

    public function query(string $selector): ?NodeQuery
    {
        if (isset($this->queries[$selector])) {
            return $this->queries[$selector];
        }
        $this->queries[$selector] = new NodeQuery($selector, $this);
        return $this->queries[$selector];
    }

    /**
     * @template T
     * @param class-string<T> $type
     * @return array<T>
     */
    public function findByType(string $type)
    {
        $collections = [];
        foreach ($this->children as $child) {
            if ($child instanceof $type) {
                $collections[] = $child;
            }
            if ($child instanceof BlockNode) {
                $collections = array_merge($collections, $child->findByType($type));
            }
        }
        return $collections;
    }

    function remove(): void
    {
        if ($this->parent) {
            $this->parent->children = array_diff($this->parent->children, [$this]);
        }
    }

    function render(): string
    {

        if (!empty($this->classes)) {
            $this->attribute["class"] = implode(" ", $this->classes);
        }

        $attribute = array_map(
            fn($k, $v) => !empty($v) ? "$k=\"" . htmlspecialchars($v) . "\"" : null,
            array_keys($this->attribute),
            $this->attribute
        );

        $content = !empty($this->children)
            ? implode(" ", array_map(fn($b) => $b->render(), $this->children))
            : ($this->content ?? '');

        if (isset($this->selfClose) && $this->selfClose) {
            return "<{$this->tagName}" . (!empty($attribute) ? " " . implode(" ", $attribute) : '') . ">";
        }
        return "<{$this->tagName}" . (!empty($attribute) ? " " . implode(" ", $attribute) : '') . ">$content</{$this->tagName}>";
    }

    function __debugInfo()
    {
        return [
            "tagName" => $this->tagName,
            "children" => array_map(fn($b) => method_exists($b, '__debugInfo') ? $b->__debugInfo() : null, $this->children),
            "attribute" => $this->attribute,
            "classes" => $this->classes
        ];
    }

    function __tostring()
    {
        return $this->render();
    }
}
