<?php

namespace App\Helpers;

class SubdomainHelper
{
    /**
     * Generate subdomain URL
     */
    public static function getSubdomainUrl($subdomain, $path = '')
    {
        $protocol = request()->secure() ? 'https' : 'http';
        $domain = config('app.url');
        
        // Extract domain from config URL (remove protocol and port)
        $domain = parse_url($domain, PHP_URL_HOST);
        if (!$domain) {
            $domain = 'boletos.local'; // fallback for local development
        }
        
        $url = "{$protocol}://{$subdomain}.{$domain}";
        
        if ($path) {
            $url .= '/' . ltrim($path, '/');
        }
        
        return $url;
    }
    
    /**
     * Check if current request is from a subdomain
     */
    public static function isSubdomain()
    {
        $host = request()->getHost();
        $mainDomain = parse_url(config('app.url'), PHP_URL_HOST);
        
        if (!$mainDomain) {
            $mainDomain = 'boletos.local';
        }
        
        return $host !== $mainDomain && strpos($host, '.') !== false;
    }
    
    /**
     * Get current subdomain
     */
    public static function getCurrentSubdomain()
    {
        if (!self::isSubdomain()) {
            return null;
        }
        
        $host = request()->getHost();
        $parts = explode('.', $host);
        
        return $parts[0] ?? null;
    }
    
    /**
     * Get base domain (without subdomain)
     */
    public static function getBaseDomain()
    {
        $domain = parse_url(config('app.url'), PHP_URL_HOST);
        
        if (!$domain) {
            $domain = 'boletos.local'; // fallback for local development
        }
        
        return $domain;
    }
}