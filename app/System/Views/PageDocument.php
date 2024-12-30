<?php

namespace App\System\Views;

use App\CoreModules\Blocks\BlockNode;
use App\CoreModules\Blocks\NodeParser;
use App\CoreModules\Blocks\NodeQuery;
use App\Entity\Page;
use App\System\Theme\Theme;

class PageDocument extends BlockNode
{
    private Page $page;
    private array $data;

    public function __construct(Page $page, $data = [])
    {

        $this->page = $page;
        $this->data = $data;

        parent::__construct("html", [
            new BlockNode(
                "head",
                [
                    new BlockNode("meta", null, [
                        "charset" => "UTF-8"
                    ]),
                    new BlockNode("title", $page->getTitle() ?? "MerapiPanel"),
                    new BlockNode("meta", null, [
                        "name" => "viewport",
                        "content" => "width=device-width, initial-scale=1.0"
                    ]),
                    new BlockNode("link", null, [
                        "rel" => "stylesheet",
                        "href" => "/assets/dist/main.css"
                    ]),
                    ...($page->getHead()["children"] ?? []),
                ],
                $page->getHead()["attributes"] ?? []
            ),
            new BlockNode(
                "body",
                $page->getBody()["children"] ?? [],
                $page->getBody()["attributes"] ?? []
            ),
            new BlockNode("script", null, ["src" => "/assets/dist/main.js", "id" => "main-js"]),
        ]);
    }

    function head(): NodeQuery
    {
        return $this->query("head");
    }

    function setTitle(string $title)
    {
        $this->query("title")->setContent($title);
    }

    function body(): NodeQuery
    {
        return $this->query("body");
    }

    private function minifyStyle(BlockNode $block)
    {
        $content = $block->getChildren()[0]->render() ?? "";
        $content = preg_replace('/\/\*.*?\*\//s', '', $content);
        $content = preg_replace('/\s+/', ' ', $content);
        $content = preg_replace('/\s*([{};,:>])\s*/', '$1', $content);
        $content = preg_replace('/;(?=\s*})/', '', $content);
        $content = trim($content);
        $block->setContent($content);
        $this->head()->append($block);
    }

    private function minifyScript(BlockNode $block)
    {
        if ($block->getTag() == "script") {

            $content = ($block->getChildren()[0] ?? null)?->render() ?? "";

            // Remove comments (both single-line and multi-line)
            $content = preg_replace('/\/\*.*?\*\/|\/\/.*(?=[\n\r])/s', '', $content);

            $content = preg_replace('/(=\s.*[^;](?<!;))/m', '$1;', $content);

            // Remove unnecessary whitespace
            $content = preg_replace('/\s+/', ' ', $content);

            // Insert semicolons at the end of statements if missing
            // Add semicolon after a closing bracket if followed by a statement start (e.g., a variable definition)
            $content = preg_replace('/\}\s*(?=[\w\$\_])/', '};', $content);
            $content = preg_replace('/(\=\s.*)/', '$1;', $content);

            // Add semicolon after statements that should be terminated
            $content = preg_replace('/([^\;\}\{])\s*(?=[\}\n])/', '$1;', $content);

            // Remove space before and after certain characters
            $content = preg_replace('/\s*([{};,:()])\s*/', '$1', $content);

            // remove uneed semicolon
            $content = preg_replace('/\;\}/', '}', $content);
            $content = preg_replace('/\;\;/', ';', $content);

            // Trim the final content
            $content = trim($content);
            $block->setContent(trim($content));
            $this->body()->append($block);
            return;
        }
    }
    function __tostring()
    {
        $this->minifyScript($this->body()->get(0));
        $this->minifyStyle($this->body()->get(0));
        return $this->render();
    }
}
