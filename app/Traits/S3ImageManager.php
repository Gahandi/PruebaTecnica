<?php

namespace App\Traits;

use Illuminate\Support\Str;

use Illuminate\Support\Facades\Storage;
//use Storage;
use Aws\S3\S3Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
trait S3ImageManager { 

    public function saveImages($base64Image, $folder, $productId)
    {
        // Decodificar la imagen base64
        $imageData = base64_decode($base64Image);

        // Generar un nombre de archivo único
        // $fileName = uniqid() . '.*'; // Puedes ajustar la extensión según tu necesidad

        // Ruta completa en el sistema de archivos de S3
        $filePath = env('S3_ENVIRONMENT'). '/' ."{$folder}/{$productId}";

        // Almacenar la imagen en S3
        Storage::disk('s3')->put($filePath, $imageData);

        // Devolver la ruta completa del archivo almacenado
        return Storage::disk('s3')->url($filePath);
    }

    function getS3ImageUrl($folder, $imageName)
    {
        
        // Adjust the path based on your S3 folder structure
        $path = env('S3_ENVIRONMENT'). '/' . $folder . '/' . $imageName;
    
        // Get the public URL for the file
        return Storage::disk('s3')->url($path);

    }

    function deleteS3Image($folder, $filename) {
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
    
        try {
            $s3->deleteObject([
                'Bucket' => $bucket,
                'Key'    => $objectKey,
            ]);
    
    
            return true;
        } catch (Exception $e) {
            // Handle the exception, log error, or return false based on your requirements
            // For example: logError("Error deleting image from S3: $objectKey - " . $e->getMessage());
    
            return false;
        }
    }


}