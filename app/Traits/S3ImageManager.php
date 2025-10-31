<?php

namespace App\Traits;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use Exception;

trait S3ImageManager { 

    public function saveImages($base64Image, $folder, $productId)
    {
        try {
            // Decodificar la imagen base64
            $imageData = base64_decode($base64Image, true);
            
            // Verificar que la decodificación fue exitosa
            if ($imageData === false) {
                Log::error('Error al decodificar imagen base64', [
                    'folder' => $folder,
                    'productId' => $productId
                ]);
                throw new Exception('Error al decodificar la imagen base64');
            }

            // Detectar el tipo MIME desde los primeros bytes de la imagen
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $imageData);
            finfo_close($finfo);

            // Mapear MIME type a extensión
            $extensions = [
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
            ];

            $extension = $extensions[$mimeType] ?? 'jpg';

            // Generar un nombre de archivo único con extensión
            $fileName = $productId . '.' . $extension;

            // Ruta completa en el sistema de archivos de S3
            $filePath = env('S3_ENVIRONMENT') . '/' . $folder . '/' . $fileName;

            Log::info('Intentando subir archivo a S3', [
                'filePath' => $filePath,
                'folder' => $folder,
                'size' => strlen($imageData),
                'mimeType' => $mimeType
            ]);

            // Almacenar la imagen en S3 y verificar que fue exitoso
            $result = Storage::disk('s3')->put($filePath, $imageData, 'public');
            
            if (!$result) {
                Log::error('Error al subir archivo a S3 - Storage::put retornó false', [
                    'filePath' => $filePath,
                    'folder' => $folder
                ]);
                throw new Exception('Error al subir el archivo a S3: la operación falló');
            }

            // Verificar que el archivo existe en S3
            if (!Storage::disk('s3')->exists($filePath)) {
                Log::error('Archivo no existe en S3 después de la subida', [
                    'filePath' => $filePath
                ]);
                throw new Exception('Error: el archivo no se encontró en S3 después de la subida');
            }

            // Obtener la URL del archivo
            $url = Storage::disk('s3')->url($filePath);

            Log::info('Archivo subido exitosamente a S3', [
                'filePath' => $filePath,
                'url' => $url
            ]);

            // Devolver la ruta completa del archivo almacenado
            return $url;
        } catch (Exception $e) {
            Log::error('Error en saveImages: ' . $e->getMessage(), [
                'folder' => $folder,
                'productId' => $productId,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    function getS3ImageUrl($folder, $imageName)
    {
        
        // Adjust the path based on your S3 folder structure
        $path = env('S3_ENVIRONMENT'). '/' . $folder . '/' . $imageName;
    
        // Get the public URL for the file
        return Storage::disk('s3')->url($path);

    }

    function deleteS3Image($folder, $filename) {
        $objectKey = null;
        try {
            $bucket = env("AWS_BUCKET");
            $region = env("AWS_DEFAULT_REGION");
            
            $s3 = new S3Client([
                'version' => 'latest',
                'region' => $region,
                'credentials' => [
                    'key'    => env("AWS_ACCESS_KEY_ID"),
                    'secret' => env("AWS_SECRET_ACCESS_KEY"),
                ],
            ]);
            $path = env('S3_ENVIRONMENT');
            $objectKey = $path . '/' . $folder . '/' . $filename;
        
            $s3->deleteObject([
                'Bucket' => $bucket,
                'Key'    => $objectKey,
            ]);
            
            Log::info('Archivo eliminado de S3', [
                'objectKey' => $objectKey
            ]);
            
            return true;
        } catch (Exception $e) {
            Log::error("Error eliminando imagen de S3: " . ($objectKey ?? 'N/A') . " - " . $e->getMessage(), [
                'objectKey' => $objectKey ?? 'N/A',
                'folder' => $folder,
                'filename' => $filename,
                'trace' => $e->getTraceAsString()
            ]);
            return false;
        }
    }


}