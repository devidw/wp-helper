<?php

namespace Devidw\WordPress\Helper;

class Helper
{
    /**
     * Update the version number of a WordPress theme.
     * 
     * @param string $version The new theme version.
     * @param string $theme_dir The directory of the `style.css` file of the theme.
     * 
     * @return bool True if the theme was updated, false otherwise.
     */
    public static function updateThemeVersion(string $version, string $theme_dir): bool
    {
        $theme_file = $theme_dir . '/style.css';
        $theme_data = file_get_contents($theme_file);
        $theme_data = preg_replace(
            '/Version: v?([0-9]+)\.([0-9]+)\.([0-9]+)/',
            "Version: $version",
            $theme_data,
        );
        return file_put_contents($theme_file, $theme_data) !== false;
    }
}
