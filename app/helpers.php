<?php

/**
 * Application Helper Functions
 *
 * This file contains global helper functions used throughout the application.
 * It is autoloaded via composer.json.
 */

if (!function_exists('asset_version')) {
    /**
     * Generate versioned asset URL using file modification time
     * This provides cache busting only when files actually change
     *
     * @param string $path The asset path relative to public directory
     * @return string The asset URL with version parameter
     *
     * @example
     * asset_version('js/app.js')
     * // Returns: /js/app.js?v=1731648000
     */
    function asset_version($path)
    {
        $publicPath = public_path($path);

        // If file exists, use its modification time as version
        if (file_exists($publicPath)) {
            $version = filemtime($publicPath);
            return asset($path) . '?v=' . $version;
        }

        // Fallback to app version or current timestamp
        $version = config('app.version', time());
        return asset($path) . '?v=' . $version;
    }
}
