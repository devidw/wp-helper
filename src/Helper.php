<?php

namespace Devidw\WordPress\Helper;

class Helper
{
    /**
     * Update the version number of a WordPress theme.
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

    /**
     * Image binary data into a WordPress attachment.
     * 
     * @see https://gist.github.com/hissy/7352933
     */
    public static function createAttachmentFromImage(string $filename, string $binaries, $extraArgs = []): ?int
    {
        $file = wp_upload_bits($filename, null, $binaries);

        if ($file instanceof WP_Error) {
            return null;
        }

        $fileType = wp_check_filetype($file['file'], null);

        $args = [
            'post_mime_type' => $fileType['type'],
        ];

        $args = array_merge($args, $extraArgs);

        $attachmentId = wp_insert_attachment($args, $file['file']);

        if ($attachmentId instanceof WP_Error) {
            return null;
        }

        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attachmentData = wp_generate_attachment_metadata($attachmentId, $file['file']);

        wp_update_attachment_metadata($attachmentId,  $attachmentData);

        return $attachmentId;
    }

    /**
     * Is current page `wp-login.php`?
     * 
     * @see https://wordpress.stackexchange.com/a/237285/218274
     */
    public static function isLoginPage(): bool
    {
        $abspath = str_replace(['\\', '/'], DIRECTORY_SEPARATOR, ABSPATH);

        return ((in_array($abspath . 'wp-login.php', get_included_files()) || in_array($abspath . 'wp-register.php', get_included_files())) ||
            (isset($_GLOBALS['pagenow']) && $GLOBALS['pagenow'] === 'wp-login.php') ||
            $_SERVER['PHP_SELF'] == '/wp-login.php');
    }

    /**
     * Get the full URL.
     */
    public static function getFullUrl(): string
    {
        return home_url() . add_query_arg([]);
    }
}
