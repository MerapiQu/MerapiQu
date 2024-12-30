<?php

namespace App\ContentManagement;

use App\Entity\Page;
use App\Entity\PageStatus;
use Throwable;

class PagesManager
{

    private array $paths = [];
    private array $pages = [];


    function __construct(array $paths = [])
    {
        $this->paths = $paths;
        $this->syncExistPages();
    }

    function getPages(): array
    {
        return $this->pages;
    }

    private function syncExistPages()
    {
        foreach ($this->paths as $path) {
            foreach (glob("$path/*.json") as $pagePath) {
                try {
                    $meta = json_decode(file_get_contents($pagePath), true);
                    $page = new Page();
                    $page->setTitle($meta['title'] ?? "");
                    $page->setStatus(
                        isset($meta['status']) && $meta['status'] == PageStatus::PUBLIC
                            ? PageStatus::PRIVATE
                            : PageStatus::PUBLIC
                    );
                    $page->setPath($meta['path'] ?? "");
                    $page->setHead($meta['head'] ?? null);
                    $page->setBody($meta['body'] ?? null);
                    $this->pages[$pagePath] = $page;
                } catch (Throwable $e) {
                    error_log($e);
                }
            }
        }
    }
}
