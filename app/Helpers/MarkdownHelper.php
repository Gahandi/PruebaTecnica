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
    protected static ?MarkdownConverter $converter = null;

    /**
     * Inicializar el convertidor de Markdown con todas las extensiones
     */
    protected static function getConverter(): MarkdownConverter
    {
        if (self::$converter === null) {
            $config = [
                'html_input' => 'strip', // Eliminar HTML inseguro
                'allow_unsafe_links' => false,
                'max_nesting_level' => 100,
                'table' => [
                    'wrap' => [
                        'enabled' => true,
                        'tag' => 'div',
                        'attributes' => ['class' => 'table-responsive'],
                    ],
                    'alignment_attributes' => [
                        'left' => ['align' => 'left'],
                        'center' => ['align' => 'center'],
                        'right' => ['align' => 'right'],
                    ],
                ],
            ];

            $environment = new Environment($config);

            // Extensión principal de CommonMark
            $environment->addExtension(new CommonMarkCoreExtension());

            // GitHub Flavored Markdown (incluye tablas, strikethrough, autolinks, task lists)
            $environment->addExtension(new GithubFlavoredMarkdownExtension());

            self::$converter = new MarkdownConverter($environment);
        }

        return self::$converter;
    }

    /**
     * Convertir Markdown a HTML con soporte completo para:
     * - Encabezados (h1-h6)
     * - Negritas (**texto** o __texto__)
     * - Cursivas (*texto* o _texto_)
     * - Tachado (~~texto~~)
     * - Listas con viñetas (- item o * item)
     * - Listas numeradas (1. item)
     * - Listas de tareas (- [ ] item o - [x] item)
     * - Enlaces ([texto](url))
     * - Imágenes (![alt](url))
     * - Tablas (sintaxis GFM)
     * - Código inline (`código`)
     * - Bloques de código (```)
     * - Citas (> texto)
     * - Líneas horizontales (---)
     */
    public static function render(?string $markdown): string
    {
        if (empty($markdown)) {
            return '';
        }

        try {
            $result = self::getConverter()->convert($markdown);
            return $result->getContent();
        } catch (\Exception $e) {
            // En caso de error, devolver el texto escapado
            return '<p>' . nl2br(e($markdown)) . '</p>';
        }
    }

    /**
     * Renderizar markdown de forma segura (escapando el contenido primero)
     * Útil para contenido que podría contener HTML malicioso
     */
    public static function renderSafe(?string $markdown): string
    {
        if (empty($markdown)) {
            return '';
        }

        // Primero escapar el contenido, luego renderizar
        return self::render(e($markdown));
    }
}
