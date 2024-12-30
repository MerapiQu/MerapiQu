<?php

namespace App\CoreModules\Blocks;

use Countable;
use Iterator;
use Override;

class NodeQuery implements Iterator, Countable
{
    private string $selector;
    private BlockNode $parent;
    public array $matchedNodes = [];
    public readonly int $count;

    public function __construct(string $selector, BlockNode $parent)
    {
        $this->selector = $selector;
        $this->parent = $parent;
        $this->matchedNodes = $this->getNodes();
        $this->count = count($this->matchedNodes);
    }

    private function getNodes(): array
    {
        $selector = $this->selector;
        $parts = preg_split('/\s*(>| )\s*/', trim($selector), -1, PREG_SPLIT_DELIM_CAPTURE);

        $currentNodes = [$this->parent];
        $isChildCombinator = false;

        foreach ($parts as $part) {
            if ($part === '>') {
                $isChildCombinator = true;
                continue;
            }

            $matchedNodes = [];

            foreach ($currentNodes as $node) {
                $children = $isChildCombinator
                    ? $node->getChildren()
                    : $this->getAllDescendants($node);

                foreach ($children as $child) {
                    if ($this->matchesSelector($child, $part)) {
                        $matchedNodes[] = $child;
                    }
                }
            }

            $isChildCombinator = false;
            if (empty($matchedNodes)) {
                return [];
            }

            $currentNodes = $matchedNodes;
        }

        return $currentNodes;
    }

    /**
     * Get all descendants of a node.
     */
    private function getAllDescendants(BlockNode $node): array
    {
        $descendants = [];
        foreach ($node->getChildren() as $child) {
            if (!($child instanceof BlockNode)) continue;
            $descendants[] = $child;
            $descendants = array_merge($descendants, $this->getAllDescendants($child));
        }
        return $descendants;
    }

    /**
     * Checks if a node matches a CSS selector (ID, class, tag name, or combinations).
     */
    private function matchesSelector(BlockNode $node, string $selector): bool
    {
        // Match tag, ID, class, and attribute selectors
        $pattern = '/^(?<tag>\w+)?(?<id>#[a-zA-Z0-9_-]+)?(?<classes>(\.[a-zA-Z0-9_-]+)*)(?<attributes>(\[[^\]]+\])*)$/';
        if (preg_match($pattern, $selector, $matches)) {
            $tag = $matches['tag'] ?? null;
            $id = isset($matches['id']) ? ltrim($matches['id'], '#') : null;
            $classes = isset($matches['classes'])
                ? array_filter(
                    explode('.', ltrim($matches['classes'], '.')),
                    fn($v) => !empty(trim($v))
                )
                : [];
            $attributes = isset($matches['attributes']) ? $matches['attributes'] : '';

            // Check tag name
            if ($tag && $node->tagName !== $tag) {
                return false;
            }

            // Check ID
            if ($id && ($node->attribute['id'] ?? null) !== $id) {
                return false;
            }

            // Check classes
            $nodeClasses = $node->classes ?? [];
            if (!empty($classes) && array_diff($classes, $nodeClasses)) {
                return false;
            }

            // Check attributes
            if (!empty($attributes)) {
                $attributePattern = '/\[(?<name>[a-zA-Z0-9_-]+)(=(?<value>[^\]]+))?\]/';
                preg_match_all($attributePattern, $attributes, $attributeMatches, PREG_SET_ORDER);

                foreach ($attributeMatches as $attr) {
                    $name = $attr['name'];
                    $value = $attr['value'] ?? null;

                    // Check if attribute exists
                    if (!isset($node->attribute[$name])) {
                        return false;
                    }

                    // Check attribute value if specified
                    if ($value !== null && $node->attribute[$name] !== trim($value, "'\"")) {
                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * get node by index
     * @param int $index
     * @return mixed
     */
    public function get(int $index = 0): BlockNode|null
    {
        if (isset($this->matchedNodes[$index]))
            return $this->matchedNodes[$index];
        return null;
    }

    /**
     * set node attribute replace old attribute
     * @param array $attribute
     * @param mixed $index
     * @return void
     */
    public function setAttribute(array $attribute, $index = 0)
    {
        $this->get($index)?->setAttribute($attribute);
    }

    /**
     * combine attribute with another attribute
     * @param array $attribute
     * @param mixed $index
     * @return void
     */
    public function appendAttribute(array $attribute, $index = 0)
    {
        $this->get($index)?->appendAttribute($attribute);
    }

    /**
     * get parent node
     * @param mixed $index
     * @return mixed
     */
    public function getParent($index = 0)
    {
        return $this->get($index)?->getParent();
    }

    /**
     * add node attribute
     * @param string $attribute
     * @param mixed $value
     * @param mixed $index
     * @return void
     */
    public function query(string $selector, int $index = 0): ?NodeQuery
    {
        return $this->get($index)?->query($selector);
    }
    public function setContent(string|array|Node $content, int $index = 0): self
    {
        $this->get($index)?->setContent($content);
        return $this;
    }

    public function append(array|Node|string $node, int $index = 0): self
    {
        $this->get($index)?->append($node);
        return $this;
    }

    function addAttr($name, $value, $index = 0)
    {
        $this->get($index)?->addAttr($name, $value);
        return $this;
    }

    public function getChildren(int $index = 0): array
    {
        return $this->get($index)?->getChildren() ?? [];
    }

    public function addClass(string $class, int $index = 0): self
    {
        $this->get($index)?->addClass($class);
        return $this;
    }

    public function getClasses(int $index = 0): ?array
    {
        return $this->get($index)?->getClasses() ?? null;
    }

    public function hasClass(string $class, int $index = 0): bool
    {
        return $this->get($index)?->hasClass($class) ?? false;
    }

    public function prepend(array|Node|string $inode, int $index = 0): self
    {
        $this->get($index)?->prepend($inode);
        return $this;
    }

    public function removeClass(string $class, int $index = 0): self
    {
        $this->get($index)?->removeClass($class);
        return $this;
    }


    public function remove(int $index = 0): self
    {
        $this->get($index)?->remove();
        return $this;
    }

    private int $position = 0;
    #[Override]
    function current(): Node|BlockNode|TextNode
    {
        return $this->matchedNodes[$this->position];
    }
    #[Override]
    function key(): int
    {
        return $this->position;
    }
    #[Override]
    function next(): void
    {
        $this->position++;
    }
    #[Override]
    function rewind(): void
    {
        $this->position = 0;
    }
    #[Override]
    function valid(): bool
    {
        return $this->position < count($this->matchedNodes);
    }
    function count(): int
    {
        return count($this->matchedNodes);
    }



    public function toArray(): array
    {
        return $this->matchedNodes;
    }

    public function __toString(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT);
    }

    function render($index = 0): string
    {
        return $this->matchedNodes[$index]?->render();
    }
}
