<?php

namespace App\Helpers;

class UrlHelper
{
    /**
     * Get the correct base URL for the application
     * This handles subdirectory deployments
     */
    public static function getBaseUrl()
    {
        $baseUrl = request()->getSchemeAndHttpHost();
        $path = request()->getBasePath();
        
        // If we're in a subdirectory, include it in the base URL
        if ($path && $path !== '/') {
            $baseUrl .= $path;
        }
        
        return $baseUrl;
    }
    
    /**
     * Generate a full URL for a given route
     */
    public static function route($route, $parameters = [])
    {
        return self::getBaseUrl() . '/' . ltrim($route, '/');
    }
}
