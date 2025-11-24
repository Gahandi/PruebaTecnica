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

    public function saveImages($fileContents, $folder, $productId)
    {
        try {
            // Validar que las credenciales de AWS estén configuradas
            $awsKey = trim(env('AWS_ACCESS_KEY_ID', ''));
            $awsSecret = trim(env('AWS_SECRET_ACCESS_KEY', ''));
            $awsBucket = trim(env('AWS_BUCKET', ''));
            $awsRegion = trim(env('AWS_DEFAULT_REGION', ''));
            \Log::info("DEBUG AWS (saveImages):", [
                'KEY_ID' => $awsKey ? 'Found' : 'EMPTY',
                'BUCKET' => $awsBucket ? 'Found' : 'EMPTY',
                'REGION' => $awsRegion ? 'Found' : 'EMPTY',
                // No mostrar la clave secreta por seguridad
                'SECRET' => $awsSecret ? 'Found' : 'EMPTY', 
                'S3_ENV' => env('S3_ENVIRONMENT') 
            ]);
            
            if (empty($awsKey) || empty($awsSecret) || empty($awsBucket) || empty($awsRegion)) {
                $missing = [];
                if (empty($awsKey)) $missing[] = 'AWS_ACCESS_KEY_ID';
                if (empty($awsSecret)) $missing[] = 'AWS_SECRET_ACCESS_KEY';
                if (empty($awsBucket)) $missing[] = 'AWS_BUCKET';
                if (empty($awsRegion)) $missing[] = 'AWS_DEFAULT_REGION';
                
                Log::error('Credenciales de AWS no configuradas', [
                    'missing' => $missing
                ]);
                throw new Exception('Error de configuración: Las credenciales de AWS no están configuradas correctamente. Faltan: ' . implode(', ', $missing));
            }
            
            // Asigna el contenido directamente
            $imageData = $fileContents;

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
            try {
                $result = Storage::disk('s3')->put($filePath, $imageData);
                
                if (!$result) {
                    Log::error('Error al subir archivo a S3 - Storage::put retornó false', [
                        'filePath' => $filePath,
                        'folder' => $folder
                    ]);
                    throw new Exception('Error al subir el archivo a S3: la operación falló');
                }
            } catch (\League\Flysystem\UnableToWriteFile $e) {
                $errorMessage = $e->getMessage();
                
                // Verificar si es un error de autenticación
                if (stripos($errorMessage, 'SignatureDoesNotMatch') !== false) {
                    Log::error('Error de autenticación con AWS S3 - SignatureDoesNotMatch', [
                        'filePath' => $filePath,
                        'folder' => $folder,
                        'awsAccessKeyId' => substr(env('AWS_ACCESS_KEY_ID', ''), 0, 8) . '...',
                        'hint' => 'El AWS_SECRET_ACCESS_KEY no corresponde al AWS_ACCESS_KEY_ID. Verifica las credenciales en tu archivo .env'
                    ]);
                    throw new Exception('Error de autenticación con AWS S3: El AWS_SECRET_ACCESS_KEY no corresponde al AWS_ACCESS_KEY_ID. Verifica que ambas credenciales en tu archivo .env sean del mismo usuario/clave en AWS.');
                }
                
                // Si no es el error específico, relanzar la excepción
                throw $e;
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
        } catch (\League\Flysystem\UnableToWriteFile $e) {
            $errorMessage = $e->getMessage();
            
            // Detectar errores específicos de AWS
            if (strpos($errorMessage, 'SignatureDoesNotMatch') !== false) {
                Log::error('Error de autenticación con AWS S3 - SignatureDoesNotMatch', [
                    'folder' => $folder,
                    'productId' => $productId,
                    'hint' => 'Verifica que AWS_ACCESS_KEY_ID y AWS_SECRET_ACCESS_KEY sean correctos y no tengan espacios en blanco'
                ]);
                throw new Exception('Error de autenticación con AWS S3: Las credenciales (Access Key o Secret Key) son incorrectas o tienen espacios en blanco. Verifica tu archivo .env');
            } elseif (strpos($errorMessage, '403') !== false || strpos($errorMessage, 'Forbidden') !== false) {
                Log::error('Error de permisos con AWS S3', [
                    'folder' => $folder,
                    'productId' => $productId,
                    'error' => $errorMessage
                ]);
                throw new Exception('Error de permisos con AWS S3: Verifica que las credenciales tengan permisos para escribir en el bucket.');
            } else {
                Log::error('Error al subir archivo a S3', [
                    'folder' => $folder,
                    'productId' => $productId,
                    'error' => $errorMessage,
                    'trace' => $e->getTraceAsString()
                ]);
                throw new Exception('Error al subir el archivo a S3: ' . $errorMessage);
            }
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
            $bucket = trim(env("AWS_BUCKET", ''));
            $region = trim(env("AWS_DEFAULT_REGION", ''));
            $awsKey = trim(env("AWS_ACCESS_KEY_ID", ''));
            $awsSecret = trim(env("AWS_SECRET_ACCESS_KEY", ''));
            
            // --- INICIO DEPURACIÓN ---
            \Log::info("DEBUG AWS (deleteS3Image):", [
                'KEY_ID' => $awsKey ? 'Found' : 'EMPTY',
                'BUCKET' => $bucket ? 'Found' : 'EMPTY',
                'REGION' => $region ? 'Found' : 'EMPTY',
                // No mostrar la clave secreta por seguridad
                'SECRET' => $awsSecret ? 'Found' : 'EMPTY',
                'S3_ENV' => env('S3_ENVIRONMENT') 
            ]);

            if (empty($bucket) || empty($region) || empty($awsKey) || empty($awsSecret)) {
                Log::error('Credenciales de AWS no configuradas para eliminar archivo');
                return false;
            }
            
            $s3 = new S3Client([
                'version' => 'latest',
                'region' => $region,
                'credentials' => [
                    'key'    => $awsKey,
                    'secret' => $awsSecret,
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