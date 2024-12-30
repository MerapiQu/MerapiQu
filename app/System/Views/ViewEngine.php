<?php

namespace App\System\Views;

use App\CoreModules\Blocks\NodeParser;
use App\System\Views\Extensions\Assets;
use App\System\Views\Extensions\Environment as ExtensionsEnvironment;
use Symfony\Component\Filesystem\Path;
use Twig\Environment;

class ViewEngine
{
    private static Environment $_twig;
    protected Environment $twig;
    private ViewDocument $document;

    public function __construct(ViewDocument $viewDocument)
    {
        $this->document = $viewDocument;
        $this->twig = $this->initTwig();
    }

    private function initTwig()
    {
        if (!isset(self::$_twig)) {

            self::$_twig = new Environment(
                new ViewLoader(),
                [
                    'autoescape' => 'html',
                ]
            );
            self::$_twig->addExtension(new Assets(
                $this->document
            ));
            self::$_twig->addExtension(new ExtensionsEnvironment($this->document));
        }
        return self::$_twig;
    }

    function render(string $name, $data = [])
    {

        $target  = $this->twig->load($name);
        $target->render(["document" => $this->document]);

        if (Path::hasExtension($target->getSourceContext()->getPath(), ["twig", "html"])) {
            if ($target->hasBlock("script")) {

                $this->document->body()->append([
                    "tagName" => "script",
                    "children" => $target->renderBlock(
                        "script",
                        [...(is_array($data) ? $data : [])]
                    )
                ]);
            }
            if ($target->hasBlock("style")) {
                $this->document->head()->append([
                    "tagName" => "style",
                    "children" => $target->renderBlock(
                        "style",
                        [...(is_array($data) ? $data : [])]
                    )
                ]);
            }

            $content = $target->hasBlock("content")
                ? $target->renderBlock(
                    "content",
                    [...(is_array($data) ? $data : [])]
                )
                : "<div>Block Content Not Found<div>";
        } else {

            $content = file_get_contents($target->getSourceContext()->getPath());
            $content = NodeParser::fromArray(json_decode($content, true));
            $html = implode("", array_map(fn($item) => $item->render(), $content));
            $content = $this->renderContent($html, $data);
        }

        $target->unwrap();
        return $content;
    }

    function renderContent($html, $data = [])
    {
        $template  = $this->twig->createTemplate($html);
        $content = $template->render(
            [
                "document" => $this->document,
                ...(is_array($data) ? $data : [])
            ]
        );
        $template->unwrap();
        return $content;
    }
}
