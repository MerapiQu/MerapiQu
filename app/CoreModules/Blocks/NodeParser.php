<?php

namespace App\CoreModules\Blocks;

use App\CoreModules\Patterns\Pattern;

class NodeParser
{


    static function toHtml(array $nodes): string
    {
        $html = '';
        foreach ($nodes as $node) {
            $html .= $node->render();
        }
        return $html;
    }

    /**
     * Summary of fromArray
     * @param array $component
     * @return array<BlockNode>
     */
    static function fromArray(array $component): array
    {
        $nodes = [];
        if (array_is_list($component)) {

            foreach ($component as $node) {
                if ($node instanceof Node) {
                    $nodes[] = $node;
                } else if (is_array($node)) {
                    $nodes = array_merge($nodes, NodeParser::fromArray($node));
                }
            }
        } else if (is_array($component)) {
            if (isset($component['pattern'])) {
                $node = new Pattern($component['pattern'], $component['data'] ?? []);
            } else if (isset($component['type'])) {
                $node = new TypedNode($component['type'], $component['data'] ?? []);
            } else {
                $node = new BlockNode(
                    $component['tagName'] ?? 'div',
                    []
                );
                if (!empty($component['attribute'])) {
                    $node->setAttribute($component['attribute']);
                }
                if (!empty($component['classes'])) {
                    $node->setClasses($component['classes']);
                }

                if (isset($component['children'])) {
                    if (is_array($component['children'])) {
                        foreach ($component['children'] as $childComponent) {
                            if (is_array($childComponent)) {
                                $node->append(NodeParser::fromArray($childComponent));
                            } else {
                                $node->append($childComponent);
                            }
                        }
                    } else {
                        $node->append(new TextNode((string)$component['children']));
                    }
                }
            }
            $nodes[] = $node;
        } else if (is_string($component)) {
            if (self::isHTML($component)) {
                $htmlNodes = NodeParser::fromHTML($component);
                $nodes = is_array($htmlNodes) ? $htmlNodes : [$htmlNodes];
            } else {
                $nodes[] = new TextNode($component);
            }
        }
        return $nodes;
    }

    /**
     * Summary of fromHTML
     * @param string $html
     * @param callable(\App\CoreModules\Blocks\BlockNode): \App\CoreModules\Blocks\BlockNode | null $callback callback for each node
     * @return \App\CoreModules\Blocks\BlockNode|array
     */
    static function fromHTML(string $html, callable|null $callback = null): BlockNode|array
    {
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML("<div>$html</div>", LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $blocks = [];
        foreach ($dom->firstChild->childNodes as $childNode) {
            if ($childNode instanceof \DOMElement) {
                $block = self::parseElement($childNode, $callback);
                $blocks[] = $callback ? $callback($block) : $block;
            }
        }

        return $blocks;
    }

    private static function parseElement(\DOMElement $element, callable|null $callback = null): BlockNode
    {

        $tagName = $element->tagName;
        $attribute = [];
        $classes = [];

        foreach ($element->attributes as $attr) {
            $attribute[$attr->nodeName] = $attr->nodeValue;
        }
        if (isset($attribute['class'])) {
            $classes = explode(' ', $attribute['class']);
            unset($attribute['class']);
        }

        $block = new BlockNode($tagName, []);
        $block->setClasses($classes);
        $block->setAttribute($attribute);

        foreach ($element->childNodes as $child) {
            if ($child instanceof \DOMElement) {
                $child = self::parseElement($child, $callback);
                $child = $callback ? $callback($child) : $child;
            } elseif ($child instanceof \DOMText && trim($child->textContent) !== '') {
                $child = new TextNode($child->textContent);
            }
            $block->append($child);
        }

        return $block;
    }

    public static function isHTML(string $string): bool
    {
        return preg_match('/^<(\w+)[^>]*>.*/s', trim($string)) === 1;
    }
}
