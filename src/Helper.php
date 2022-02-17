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

    /**
     * Image binary data into a WordPress attachment.
     * 
     * @see https://gist.github.com/hissy/7352933
     * 
     * @param string $filename The name of the file.
     * @param string $binaries The binary data of the image.
     * 
     * @return int|null The attachment ID.
     */
    public static function createAttachmentFromImage(string $filename, string $binaries): ?int
    {
        $file = wp_upload_bits($filename, null, $binaries);

        if ($file instanceof WP_Error) {
            return null;
        }

        $fileType = wp_check_filetype($file['file'], null);

        $attachment = [
            'post_mime_type' => $fileType['type'],
            'post_title' => $filename,
        ];

        $attachmentId = wp_insert_attachment($attachment, $file['file']);

        if ($attachmentId instanceof WP_Error) {
            return null;
        }

        require_once(ABSPATH . 'wp-admin/includes/image.php');

        $attachmentData = wp_generate_attachment_metadata($attachmentId, $file['file']);

        wp_update_attachment_metadata($attachmentId,  $attachmentData);

        return $attachmentId;
    }
}
