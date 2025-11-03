<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use League\Flysystem\UnableToWriteFile;

class CheckAWSConfig extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aws:check-config';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verifica la configuración de AWS S3 y prueba la conexión';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verificando configuración de AWS S3...');
        $this->newLine();

        // Obtener valores del .env
        $awsKey = trim(env('AWS_ACCESS_KEY_ID', ''));
        $awsSecret = trim(env('AWS_SECRET_ACCESS_KEY', ''));
        $awsBucket = trim(env('AWS_BUCKET', ''));
        $awsRegion = trim(env('AWS_DEFAULT_REGION', ''));
        $awsEndpoint = env('AWS_ENDPOINT');
        $s3Environment = env('S3_ENVIRONMENT');

        // Verificar que las variables estén configuradas
        $this->info('Variables de entorno:');
        $this->line('  AWS_ACCESS_KEY_ID: ' . ($awsKey ? substr($awsKey, 0, 8) . '...' : '❌ NO CONFIGURADO'));
        $this->line('  AWS_SECRET_ACCESS_KEY: ' . ($awsSecret ? '✓ Configurado (' . strlen($awsSecret) . ' caracteres)' : '❌ NO CONFIGURADO'));
        $this->line('  AWS_BUCKET: ' . ($awsBucket ?: '❌ NO CONFIGURADO'));
        $this->line('  AWS_DEFAULT_REGION: ' . ($awsRegion ?: '❌ NO CONFIGURADO'));
        $this->line('  AWS_ENDPOINT: ' . ($awsEndpoint ?: 'No configurado (usando endpoint por defecto)'));
        $this->line('  S3_ENVIRONMENT: ' . ($s3Environment ?: '❌ NO CONFIGURADO'));
        $this->newLine();

        // Verificar espacios en blanco
        if ($awsKey && (trim($awsKey) !== $awsKey || preg_match('/\s/', $awsKey))) {
            $this->warn('⚠ ADVERTENCIA: AWS_ACCESS_KEY_ID contiene espacios en blanco o caracteres extra');
        }
        if ($awsSecret && (trim($awsSecret) !== $awsSecret || preg_match('/\s/', $awsSecret))) {
            $this->warn('⚠ ADVERTENCIA: AWS_SECRET_ACCESS_KEY contiene espacios en blanco o caracteres extra');
        }

        // Verificar que todas las variables estén configuradas
        if (empty($awsKey) || empty($awsSecret) || empty($awsBucket) || empty($awsRegion)) {
            $this->error('❌ Error: Faltan variables de entorno requeridas.');
            $this->line('Asegúrate de configurar todas las variables en tu archivo .env');
            return 1;
        }

        // Intentar conectar con S3
        $this->info('Probando conexión con S3...');
        try {
            $s3Client = new S3Client([
                'version' => 'latest',
                'region' => $awsRegion,
                'credentials' => [
                    'key'    => $awsKey,
                    'secret' => $awsSecret,
                ],
            ]);

            // Verificar que el bucket existe y es accesible
            try {
                $s3Client->headBucket(['Bucket' => $awsBucket]);
                $this->info('✓ Bucket existe y es accesible: ' . $awsBucket);
            } catch (UnableToWriteFile $e) {
                // Capturar excepciones de Flysystem que envuelven errores de AWS
                $errorMessage = $e->getMessage();
                
                if (stripos($errorMessage, 'SignatureDoesNotMatch') !== false) {
                    $this->error('❌ Error de autenticación: SignatureDoesNotMatch');
                    $this->newLine();
                    $this->line('Este error significa que el AWS_SECRET_ACCESS_KEY es incorrecto.');
                    $this->newLine();
                    $this->line('Posibles causas:');
                    $this->line('  1. El AWS_SECRET_ACCESS_KEY en tu archivo .env es incorrecto');
                    $this->line('  2. Hay espacios en blanco alrededor del valor en .env');
                    $this->line('  3. Las credenciales han sido rotadas o revocadas en AWS');
                    $this->newLine();
                    $this->line('Solución:');
                    $this->line('  - Verifica y copia nuevamente el AWS_SECRET_ACCESS_KEY desde la consola de AWS');
                    $this->line('  - Asegúrate de que en .env NO haya comillas o espacios:');
                    $this->line('    ✅ CORRECTO: AWS_SECRET_ACCESS_KEY=tu_clave_secreta');
                    $this->line('    ❌ INCORRECTO: AWS_SECRET_ACCESS_KEY="tu_clave_secreta"');
                    $this->line('    ❌ INCORRECTO: AWS_SECRET_ACCESS_KEY= tu_clave_secreta ');
                    $this->line('  - Después de cambiar, ejecuta: php artisan config:clear');
                } else {
                    $this->error('❌ Error al acceder al bucket: ' . $errorMessage);
                }
                return 1;
            } catch (AwsException $e) {
                $errorCode = $e->getAwsErrorCode();
                $errorMessage = $e->getAwsErrorMessage();
                $fullExceptionMessage = $e->getMessage();
                
                // Buscar SignatureDoesNotMatch en el mensaje completo (puede venir en el XML de respuesta)
                // Buscar en múltiples lugares porque puede venir en diferentes formatos
                $messageToSearch = strtolower($fullExceptionMessage . ' ' . $errorMessage . ' ' . ($errorCode ?? ''));
                $hasSignatureError = (
                    $errorCode === 'SignatureDoesNotMatch' || 
                    stripos($errorMessage, 'SignatureDoesNotMatch') !== false ||
                    stripos($fullExceptionMessage, 'SignatureDoesNotMatch') !== false ||
                    stripos($messageToSearch, 'signaturedoesnotmatch') !== false
                );
                
                if ($hasSignatureError) {
                    $this->error('❌ Error de autenticación: SignatureDoesNotMatch');
                    $this->newLine();
                    $this->line('Este error significa que el AWS_SECRET_ACCESS_KEY es incorrecto.');
                    $this->newLine();
                    $this->line('Posibles causas:');
                    $this->line('  1. El AWS_SECRET_ACCESS_KEY en tu archivo .env es incorrecto');
                    $this->line('  2. Hay espacios en blanco alrededor del valor en .env');
                    $this->line('  3. Las credenciales han sido rotadas o revocadas en AWS');
                    $this->newLine();
                    $this->line('Solución:');
                    $this->line('  - Verifica y copia nuevamente el AWS_SECRET_ACCESS_KEY desde la consola de AWS');
                    $this->line('  - Asegúrate de que en .env NO haya comillas o espacios:');
                    $this->line('    ✅ CORRECTO: AWS_SECRET_ACCESS_KEY=tu_clave_secreta');
                    $this->line('    ❌ INCORRECTO: AWS_SECRET_ACCESS_KEY="tu_clave_secreta"');
                    $this->line('    ❌ INCORRECTO: AWS_SECRET_ACCESS_KEY= tu_clave_secreta ');
                    $this->line('  - Después de cambiar, ejecuta: php artisan config:clear');
                } elseif ($errorCode === '403' || strpos($errorMessage, '403') !== false || strpos($errorMessage, 'Forbidden') !== false || strpos($fullExceptionMessage, '403') !== false) {
                    // Si el mensaje contiene SignatureDoesNotMatch, es un error de autenticación, no de permisos
                    if (strpos($fullExceptionMessage, 'SignatureDoesNotMatch') !== false || strpos($errorMessage, 'SignatureDoesNotMatch') !== false) {
                        $this->error('❌ Error de autenticación: SignatureDoesNotMatch (HTTP 403)');
                        $this->newLine();
                        $this->line('Este error significa que el AWS_SECRET_ACCESS_KEY es incorrecto.');
                        $this->newLine();
                        $this->line('Posibles causas:');
                        $this->line('  1. El AWS_SECRET_ACCESS_KEY en tu archivo .env es incorrecto');
                        $this->line('  2. Hay espacios en blanco alrededor del valor en .env');
                        $this->line('  3. Las credenciales han sido rotadas o revocadas en AWS');
                        $this->newLine();
                        $this->line('Solución:');
                        $this->line('  - Verifica y copia nuevamente el AWS_SECRET_ACCESS_KEY desde la consola de AWS');
                        $this->line('  - Asegúrate de que en .env NO haya comillas o espacios:');
                        $this->line('    ✅ CORRECTO: AWS_SECRET_ACCESS_KEY=tu_clave_secreta');
                        $this->line('    ❌ INCORRECTO: AWS_SECRET_ACCESS_KEY="tu_clave_secreta"');
                        $this->line('    ❌ INCORRECTO: AWS_SECRET_ACCESS_KEY= tu_clave_secreta ');
                        $this->line('  - Después de cambiar, ejecuta: php artisan config:clear');
                    } else {
                        $this->error('❌ Error 403 Forbidden');
                        $this->newLine();
                        $this->line('El Access Key ID que estás usando: ' . substr($awsKey, 0, 8) . '...');
                        $this->line('Este error puede significar:');
                        $this->line('  1. Las credenciales son correctas pero no tienen permisos para el bucket');
                        $this->line('  2. El AWS_SECRET_ACCESS_KEY es incorrecto (causa más común)');
                        $this->line('  3. El bucket requiere permisos específicos');
                        $this->newLine();
                        $this->line('Recomendación: Verifica el AWS_SECRET_ACCESS_KEY primero.');
                    }
                } else {
                    $this->error('❌ Error al acceder al bucket: ' . $errorMessage);
                    $this->line('Código de error: ' . $errorCode);
                }
                return 1;
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                if (strpos($errorMessage, 'SignatureDoesNotMatch') !== false) {
                    $this->error('❌ Error de autenticación: SignatureDoesNotMatch');
                    $this->line('Verifica que el AWS_SECRET_ACCESS_KEY sea correcto.');
                } else {
                    $this->error('❌ Error de conexión: ' . $errorMessage);
                }
                return 1;
            }

            // Intentar subir un archivo de prueba
            $this->info('Probando subida de archivo...');
            $testFile = 'test-' . time() . '.txt';
            $testContent = 'Test de conexión desde Laravel - ' . now();
            
            try {
                Storage::disk('s3')->put($testFile, $testContent);
                
                // Verificar que el archivo existe
                if (Storage::disk('s3')->exists($testFile)) {
                    $this->info('✓ Archivo de prueba subido exitosamente');
                    
                    // Intentar leer el archivo
                    $content = Storage::disk('s3')->get($testFile);
                    if ($content === $testContent) {
                        $this->info('✓ Archivo leído correctamente');
                    }
                    
                    // Eliminar el archivo de prueba
                    Storage::disk('s3')->delete($testFile);
                    $this->info('✓ Archivo de prueba eliminado');
                    
                    $this->newLine();
                    $this->info('✅ ¡Configuración de AWS S3 correcta!');
                    return 0;
                } else {
                    $this->error('❌ Error: El archivo no se encontró después de subirlo');
                    return 1;
                }
            } catch (\Exception $e) {
                $errorMessage = $e->getMessage();
                
                if (strpos($errorMessage, 'SignatureDoesNotMatch') !== false) {
                    $this->error('❌ Error de autenticación: SignatureDoesNotMatch');
                    $this->newLine();
                    $this->line('Este error generalmente significa:');
                    $this->line('  1. El AWS_SECRET_ACCESS_KEY es incorrecto');
                    $this->line('  2. Hay espacios en blanco al inicio o final de las credenciales en .env');
                    $this->line('  3. Las credenciales han sido rotadas o revocadas');
                    $this->newLine();
                    $this->line('Solución:');
                    $this->line('  - Verifica que AWS_SECRET_ACCESS_KEY sea correcto');
                    $this->line('  - Asegúrate de que no haya espacios en las variables del .env');
                    $this->line('  - Ejemplo correcto: AWS_SECRET_ACCESS_KEY=tu_clave_secreta');
                    $this->line('  - Ejemplo incorrecto: AWS_SECRET_ACCESS_KEY=" tu_clave_secreta "');
                } else {
                    $this->error('❌ Error al subir archivo: ' . $errorMessage);
                }
                return 1;
            }
        } catch (\Exception $e) {
            $this->error('❌ Error de conexión: ' . $e->getMessage());
            return 1;
        }
    }
}
