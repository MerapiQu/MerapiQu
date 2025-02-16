<?php

namespace MerapiPanel\System;

use Il4mb\BlockNode\Node;
use MerapiPanel\System\Views\Extensions\ScriptEx;
use MerapiPanel\System\Views\ViewLoader;
use Il4mb\BlockNode\NodeHtml;
use Il4mb\BlockNode\NodeParser;
use MerapiPanel\System\Views\Extensions\Assets;
use Symfony\Component\Filesystem\Path;
use Twig\Environment;
use Twig\Loader\LoaderInterface;

class WebEnvironment extends Environment
{

    protected NodeHtml $document;
    protected LoaderInterface $loader;
    protected string $basePath;

    function __construct()
    {
        $this->document = new NodeHtml(head: [
            "children" => [
                [
                    "tagName" => "title",
                    "children" => ""
                ],
                [
                    "tagName" => "meta",    
                    "attribute" => [
                        "charset" => "utf-8"
                    ]
                ],
                [
                    "tagName" => "meta",
                    "attribute" => [
                        "name" => "viewport",
                        "content" => "width=device-width, initial-scale=1"
                    ]
                ],
                [
                    "tagName" => "link",
                    "attribute" => [
                        "rel" => "stylesheet",
                        "href" => "/assets/dist/main.css"
                    ]
                ]
            ]
        ], body: []);
        $this->loader = new ViewLoader([
            $this->getViewPath()
        ]);

        parent::__construct(loader: $this->loader, options: []);

        $this->addExtension(new Assets($this));
        $this->addExtension(new ScriptEx($this));
    }

    function getDocument(): NodeHtml
    {
        return $this->document;
    }

    function getLoader(): LoaderInterface
    {
        return $this->loader;
    }

    function getViewPath()
    {
        return Path::join($this->basePath, "resources", "views");
    }

    function getAssetPath()
    {
        return Path::join($this->basePath, "resources", "assets");
    }

    function render($template, $context = []): string
    {

        $target = $this->load($template);
        $target->render(["document" => $this->document]);

        if (Path::hasExtension($target->getSourceContext()->getPath(), ["twig", "html"])) {
            if ($target->hasBlock("script")) {

                $this->document->body->append([
                    "tagName" => "script",
                    "children" => $target->renderBlock(
                        "script",
                        [...(is_array($context) ? $context : [])]
                    )
                ]);
            }
            if ($target->hasBlock("style")) {
                $this->document->head->append([
                    "tagName" => "style",
                    "children" => $target->renderBlock(
                        "style",
                        [...(is_array($context) ? $context : [])]
                    )
                ]);
            }

            $content = $target->hasBlock("content")
                ? $target->renderBlock(
                    "content",
                    [...(is_array($context) ? $context : [])]
                )
                : "<div>Block Content Not Found<div>";
        } else {

            $content = file_get_contents($target->getSourceContext()->getPath());
            $content = NodeParser::fromArray(json_decode($content, true));
        }
        $this->document->body->prepend($content);

        $target->unwrap();
        return $this->document;
    }
}
