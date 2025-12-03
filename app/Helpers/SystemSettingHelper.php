<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

if (!function_exists('get_video_upload_limit')) {
    /**
     * Get the video upload limit in KB
     *
     * @return int
     */
    function get_video_upload_limit()
    {
        try {
            // Get PHP's upload limit in KB (convert from bytes)
            $phpUploadLimit = (int)(ini_get('upload_max_filesize')) * 1024;
            
            // Return the PHP limit or default to 2MB if there's an issue
            return $phpUploadLimit > 0 ? $phpUploadLimit : 2048; // Default to 2MB if all else fails
        } catch (\Exception $e) {
            // If there's any error, return the PHP limit or default to 2MB
            $phpUploadLimit = (int)(ini_get('upload_max_filesize')) * 1024;
            return $phpUploadLimit > 0 ? $phpUploadLimit : 2048; // Default to 2MB if all else fails
        }
    }
}

if (!function_exists('get_video_upload_limit_mb')) {
    /**
     * Get the video upload limit in MB
     *
     * @return float
     */
    function get_video_upload_limit_mb()
    {
        return round(get_video_upload_limit() / 1024, 2);
    }
}

if (!function_exists('get_site_logo')) {
    /**
     * Get the site logo URL
     *
     * @return string|null
     */
    function get_site_logo()
    {
        try {
            // Get logo path from system settings
            $logoPath = DB::table('system_settings')->where('key', 'logo_path')->value('value');
            
            // Return the full URL if logo exists
            if ($logoPath && Storage::disk('public')->exists($logoPath)) {
                return Storage::url($logoPath);
            }
            
            // Return null if no logo or logo doesn't exist
            return null;
        } catch (\Exception $e) {
            // Return null if there's any error
            return null;
        }
    }
}