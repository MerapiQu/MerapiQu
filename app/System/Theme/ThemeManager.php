<?php

namespace App\System\Theme;

use App\Miscellaneous\Utility;
use App\System\Settings;

class ThemeManager
{
    private string $path;

    /**
     * Summary of themes
     * @var array<string, Theme>
     */
    protected array $themes = [];
    protected Settings $settings;

    function __construct()
    {
        $this->path = Utility::getcwd("/media/themes");
        $this->settings = Settings::instance();
        $this->syncExistTheme();
    }

    private function syncExistTheme()
    {
        foreach (glob($this->path . "/*", GLOB_ONLYDIR) as $themePath) {
            $this->themes[$themePath] = new Theme($themePath);
        }
    }


    function getTheme(): Theme
    {
        return array_values($this->themes)[0];
    }
}
