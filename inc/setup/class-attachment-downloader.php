<?php
namespace Polysaas\Setup;

/**
 * Class for downloading attachments from a given URL.
 */
class Attachment_Downloader {
    /**
     * Holds full path to where the files will be saved.
     *
     * @var string
     */
    private $download_directory_path = '';

    /**
     * Constructor method.
     */
    public function __construct() {
        $upload_dir = wp_upload_dir();
        $this->download_directory_path = trailingslashit($upload_dir['path']);
    }

    /**
     * Download file from a given URL.
     *
     * @param string $url URL of file to download.
     * @param string $filename Filename of the file to save.
     * @return string|WP_Error Full path to the downloaded file or WP_Error object with error message.
     */
    public function download_file($url, $filename) {
        error_log("Attachment Downloader: Starting download of {$url}");

        // Test if the URL to the file is defined
        if (empty($url)) {
            return new \WP_Error(
                'missing_url',
                'Missing URL for downloading a file!'
            );
        }

        // Get file content from the server
        $response = wp_safe_remote_get(
            $url,
            array(
                'timeout' => 300,
                'headers' => array(
                    'Accept-Encoding' => 'identity',
                    'Accept' => '*/*'
                ),
                'user-agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
                'sslverify' => false // For local development
            )
        );

        // Test if the get request was not successful
        if (is_wp_error($response)) {
            error_log("Attachment Downloader: Download failed - " . $response->get_error_message());
            return $response;
        }

        $response_code = \wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            error_log("Attachment Downloader: Invalid response code - {$response_code}");
            return new \WP_Error('http_error', "HTTP Error: {$response_code}");
        }

        // Create temp file
        $temp_file = \wp_tempnam();
        $body = \wp_remote_retrieve_body($response);
        
        if (!file_put_contents($temp_file, $body)) {
            error_log("Attachment Downloader: Failed to write to temp file");
            return new \WP_Error('temp_file_error', 'Could not create temporary file');
        }

        error_log("Attachment Downloader: Temp file created at {$temp_file}");

        // Create unique filename
        $filename = \wp_unique_filename($this->download_directory_path, $filename);
        $local_file = $this->download_directory_path . $filename;

        // Copy temp file to final location
        if (!copy($temp_file, $local_file)) {
            @unlink($temp_file);
            error_log("Attachment Downloader: Failed to copy file to {$local_file}");
            return new \WP_Error('move_error', 'Could not copy temporary file');
        }
        @unlink($temp_file); // Clean up temp file

        error_log("Attachment Downloader: File saved to {$local_file}");

        // Set proper permissions
        $stat = stat(dirname($local_file));
        $perms = $stat['mode'] & 0000666;
        chmod($local_file, $perms);

        return $local_file;
    }

    /**
     * Process attachment for import
     *
     * @param array $post_data Post data for the attachment
     * @param string $url URL of the attachment
     * @return int|WP_Error Post ID on success, WP_Error on failure
     */
    public function process_attachment($post_data, $url) {
        error_log("Attachment Downloader: Processing attachment from {$url}");

        if (empty($url)) {
            return new \WP_Error('missing_url', 'No URL provided for attachment');
        }

        // Get the file name
        $file_name = basename($url);

        // Download the file
        $downloaded_file = $this->download_file($url, $file_name);
        
        if (is_wp_error($downloaded_file)) {
            return $downloaded_file;
        }

        // Check if file is valid
        if (!file_exists($downloaded_file)) {
            error_log("Attachment Downloader: Downloaded file not found at {$downloaded_file}");
            return new \WP_Error('file_error', 'Downloaded file not found');
        }

        $file_size = filesize($downloaded_file);
        if ($file_size === 0) {
            @unlink($downloaded_file);
            error_log("Attachment Downloader: Downloaded file is empty");
            return new \WP_Error('file_error', 'Downloaded file is empty');
        }

        error_log("Attachment Downloader: File size: {$file_size} bytes");

        // Check if file is an image
        $wp_filetype = wp_check_filetype($downloaded_file, null);
        if (strpos($wp_filetype['type'], 'image/') === 0) {
            $image_size = @getimagesize($downloaded_file);
            if (!$image_size) {
                @unlink($downloaded_file);
                error_log("Attachment Downloader: Invalid image file");
                return new \WP_Error('invalid_image', 'Downloaded file is not a valid image');
            }
            error_log("Attachment Downloader: Valid image file - dimensions: {$image_size[0]}x{$image_size[1]}");
        }

        // Prepare attachment data
        $attachment = array(
            'post_mime_type' => $wp_filetype['type'],
            'post_title' => preg_replace('/\.[^.]+$/', '', $post_data['post_title']),
            'post_content' => '',
            'post_status' => 'inherit',
            'guid' => $url
        );

        if (isset($post_data['post_parent'])) {
            $attachment['post_parent'] = $post_data['post_parent'];
        }

        // Insert the attachment
        $attachment_id = wp_insert_attachment($attachment, $downloaded_file);
        
        if (is_wp_error($attachment_id)) {
            @unlink($downloaded_file);
            error_log("Attachment Downloader: Failed to insert attachment - " . $attachment_id->get_error_message());
            return $attachment_id;
        }

        // Generate metadata for the attachment
        $attachment_data = wp_generate_attachment_metadata($attachment_id, $downloaded_file);
        wp_update_attachment_metadata($attachment_id, $attachment_data);

        error_log("Attachment Downloader: Successfully created attachment {$attachment_id}");
        return $attachment_id;
    }

    /**
     * Get download_directory_path attribute.
     */
    public function get_download_directory_path() {
        return $this->download_directory_path;
    }

    /**
     * Set download_directory_path attribute.
     * If no valid path is specified, the default WP upload directory will be used.
     *
     * @param string $download_directory_path Path, where the files will be saved.
     */
    public function set_download_directory_path($download_directory_path) {
        if (file_exists($download_directory_path)) {
            $this->download_directory_path = trailingslashit($download_directory_path);
        } else {
            $upload_dir = wp_upload_dir();
            $this->download_directory_path = trailingslashit($upload_dir['path']);
        }
    }
}