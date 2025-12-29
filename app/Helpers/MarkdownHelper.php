<?php

namespace App\Helpers;

use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\Table\TableExtension;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\CommonMark\MarkdownConverter;

class MarkdownHelper
{
    /**
     * Lista de dominios permitidos para iframes
     */
    private static array $allowedIframeDomains = [
        'instagram.com',
        'www.instagram.com',
        'youtube.com',
        'www.youtube.com',
        'youtu.be',
        'vimeo.com',
        'player.vimeo.com',
        'facebook.com',
        'www.facebook.com',
        'twitter.com',
        'x.com',
        'tiktok.com',
        'www.tiktok.com',
        'spotify.com',
        'open.spotify.com',
        'soundcloud.com',
        'maps.google.com',
        'www.google.com',
        'codepen.io',
        'jsfiddle.net',
        'codesandbox.io',
    ];

    /**
     * Renderiza Markdown a HTML de forma segura
     * Permite HTML seguro, iframes de dominios confiables, imágenes externas
     */
    public static function render(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        // Pre-procesar: proteger iframes válidos antes de sanitizar
        $protectedContent = self::protectIframes($content);

        // Configurar CommonMark con extensiones
        $config = [
            'html_input' => 'allow', // Permitir HTML
            'allow_unsafe_links' => false, // No permitir javascript: links
            'max_nesting_level' => 100,
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());

        // Agregar extensiones si están disponibles
        try {
            $environment->addExtension(new GithubFlavoredMarkdownExtension());
        } catch (\Throwable $e) {
            // Si GFM no está disponible, agregar extensiones individuales
            try {
                $environment->addExtension(new TableExtension());
            } catch (\Throwable $e) {
            }
            try {
                $environment->addExtension(new StrikethroughExtension());
            } catch (\Throwable $e) {
            }
            try {
                $environment->addExtension(new AutolinkExtension());
            } catch (\Throwable $e) {
            }
            try {
                $environment->addExtension(new TaskListExtension());
            } catch (\Throwable $e) {
            }
        }

        $converter = new MarkdownConverter($environment);
        $html = $converter->convert($protectedContent)->getContent();

        // Post-procesar: restaurar iframes protegidos
        $html = self::restoreIframes($html);

        // Sanitizar HTML peligroso (scripts, onclick, etc.)
        $html = self::sanitizeHtml($html);

        return $html;
    }

    /**
     * Protege iframes válidos reemplazándolos con placeholders
     */
    private static function protectIframes(string $content): string
    {
        $placeholders = [];

        // Patrón para encontrar iframes
        $pattern = '/<iframe[^>]*src=["\']([^"\']+)["\'][^>]*>.*?<\/iframe>/is';

        $content = preg_replace_callback($pattern, function ($matches) use (&$placeholders) {
            $iframe = $matches[0];
            $src = $matches[1];

            // Verificar si el dominio está permitido
            $parsedUrl = parse_url($src);
            $host = $parsedUrl['host'] ?? '';

            foreach (self::$allowedIframeDomains as $allowedDomain) {
                if ($host === $allowedDomain || str_ends_with($host, '.' . $allowedDomain)) {
                    // Dominio permitido - proteger el iframe
                    $placeholder = '<!--IFRAME_PLACEHOLDER_' . count($placeholders) . '-->';
                    $placeholders[$placeholder] = $iframe;
                    return $placeholder;
                }
            }

            // Dominio no permitido - eliminar iframe
            return '';
        }, $content);

        // Guardar placeholders para restaurar después
        self::$currentPlaceholders = $placeholders;

        return $content;
    }

    /**
     * Placeholders actuales para iframes
     */
    private static array $currentPlaceholders = [];

    /**
     * Restaura iframes protegidos
     */
    private static function restoreIframes(string $html): string
    {
        foreach (self::$currentPlaceholders as $placeholder => $iframe) {
            // Agregar estilos responsivos al iframe
            $responsiveIframe = self::makeIframeResponsive($iframe);
            $html = str_replace(
                htmlspecialchars($placeholder),
                $responsiveIframe,
                $html
            );
            $html = str_replace($placeholder, $responsiveIframe, $html);
        }

        self::$currentPlaceholders = [];

        return $html;
    }

    /**
     * Hace un iframe responsivo
     */
    private static function makeIframeResponsive(string $iframe): string
    {
        // Agregar clases para hacerlo responsivo si no las tiene
        if (strpos($iframe, 'style=') === false) {
            $iframe = str_replace('<iframe', '<iframe style="max-width:100%;border-radius:0.5rem;border:1px solid #e5e7eb;"', $iframe);
        }

        // Envolver en contenedor responsivo
        return '<div class="iframe-container my-4" style="position:relative;overflow:hidden;max-width:100%;">' . $iframe . '</div>';
    }

    /**
     * Sanitiza HTML peligroso
     */
    private static function sanitizeHtml(string $html): string
    {
        // Eliminar scripts y eventos JavaScript
        $html = preg_replace('/<script\b[^>]*>.*?<\/script>/is', '', $html);
        $html = preg_replace('/\s+on\w+\s*=\s*["\'][^"\']*["\']/i', '', $html);
        $html = preg_replace('/\s+on\w+\s*=\s*[^\s>]+/i', '', $html);

        // Eliminar javascript: en href/src
        $html = preg_replace('/href\s*=\s*["\']javascript:[^"\']*["\']/i', 'href="#"', $html);
        $html = preg_replace('/src\s*=\s*["\']javascript:[^"\']*["\']/i', 'src=""', $html);

        // Eliminar data: URLs peligrosas (excepto imágenes base64)
        $html = preg_replace('/src\s*=\s*["\']data:(?!image\/)[^"\']*["\']/i', 'src=""', $html);

        // Eliminar tags peligrosos
        $html = preg_replace('/<\s*object\b[^>]*>.*?<\/object>/is', '', $html);
        $html = preg_replace('/<\s*embed\b[^>]*>/is', '', $html);
        $html = preg_replace('/<\s*applet\b[^>]*>.*?<\/applet>/is', '', $html);
        $html = preg_replace('/<\s*meta\b[^>]*>/is', '', $html);
        $html = preg_replace('/<\s*link\b[^>]*>/is', '', $html);
        $html = preg_replace('/<\s*base\b[^>]*>/is', '', $html);
        $html = preg_replace('/<\s*form\b[^>]*>.*?<\/form>/is', '', $html);

        // Eliminar style tags (pero mantener inline styles)
        $html = preg_replace('/<\s*style\b[^>]*>.*?<\/style>/is', '', $html);

        // Sanitizar CSS inline peligroso (expression, behavior, etc.)
        $html = preg_replace('/style\s*=\s*["\'][^"\']*expression\s*\([^"\']*["\']/i', 'style=""', $html);
        $html = preg_replace('/style\s*=\s*["\'][^"\']*behavior\s*:[^"\']*["\']/i', 'style=""', $html);
        $html = preg_replace('/style\s*=\s*["\'][^"\']*-moz-binding\s*:[^"\']*["\']/i', 'style=""', $html);

        return $html;
    }

    /**
     * Versión simple sin iframes (para resúmenes, previews)
     */
    public static function renderSimple(?string $content): string
    {
        if (empty($content)) {
            return '';
        }

        $config = [
            'html_input' => 'strip', // Eliminar HTML
            'allow_unsafe_links' => false,
        ];

        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());

        $converter = new MarkdownConverter($environment);
        return $converter->convert($content)->getContent();
    }
}
