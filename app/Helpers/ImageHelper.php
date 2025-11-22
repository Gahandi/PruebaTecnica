<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;

class ImageHelper
{
    /**
     * Obtener la URL completa de S3 desde una ruta relativa
     * 
     * @param string|null $imagePath Ruta relativa de la imagen (ej: "production/events/images/file.jpg")
     * @param string|null $default Imagen por defecto si no hay imagen
     * @return string URL completa de la imagen
     */
    public static function getImageUrl($imagePath, $default = null)
    {
        // Si no hay imagen, retornar la imagen por defecto
        if (empty($imagePath)) {
            return $default ?? 'https://via.placeholder.com/800x600?text=Sin+Imagen';
        }

        // Si ya es una URL completa (http:// o https://), retornarla tal cual
        if (filter_var($imagePath, FILTER_VALIDATE_URL)) {
            return $imagePath;
        }

        // Si es una ruta relativa, construir la URL completa desde S3
        try {
            // Normalizar la ruta (remover leading slash)
            $imagePath = ltrim($imagePath, '/');
            
            // Intentar obtener la URL usando Storage primero
            try {
                if (Storage::disk('s3')->exists($imagePath)) {
                    return Storage::disk('s3')->url($imagePath);
                }
            } catch (\Exception $e) {
                // Si falla, continuar con métodos alternativos
            }

            // Si no existe o falla Storage, construir la URL manualmente
            // Prioridad 1: Usar AWS_URL del environment
            $awsUrl = env('AWS_URL');
            if ($awsUrl) {
                // Remover trailing slash si existe
                $awsUrl = rtrim($awsUrl, '/');
                return $awsUrl . '/' . $imagePath;
            }

            // Prioridad 2: Construir desde bucket y región
            $bucket = env('AWS_BUCKET');
            $region = env('AWS_DEFAULT_REGION', 'us-east-1');
            
            if ($bucket) {
                // Formato estándar de URL de S3
                // https://bucket-name.s3.region.amazonaws.com/path/to/file.jpg
                return "https://{$bucket}.s3.{$region}.amazonaws.com/{$imagePath}";
            }

            // Si todo falla, retornar la imagen por defecto
            \Log::warning('No se pudo construir URL de imagen de S3', [
                'imagePath' => $imagePath,
                'awsUrl' => $awsUrl,
                'bucket' => $bucket
            ]);
            return $default ?? 'https://via.placeholder.com/800x600?text=Sin+Imagen';
        } catch (\Exception $e) {
            \Log::error('Error obteniendo URL de imagen de S3', [
                'imagePath' => $imagePath,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return $default ?? 'https://via.placeholder.com/800x600?text=Sin+Imagen';
        }
    }

    /**
     * Obtener múltiples URLs de imágenes
     * 
     * @param array $imagePaths Array de rutas relativas
     * @param string|null $default Imagen por defecto
     * @return array Array de URLs completas
     */
    public static function getImageUrls(array $imagePaths, $default = null)
    {
        return array_map(function($path) use ($default) {
            return self::getImageUrl($path, $default);
        }, $imagePaths);
    }
}

