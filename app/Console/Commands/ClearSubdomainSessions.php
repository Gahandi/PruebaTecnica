<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearSubdomainSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:clear-subdomain';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all sessions to fix subdomain session issues';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧹 Limpiando sesiones para subdominios...');
        
        // Limpiar sesiones de archivos
        $sessionPath = storage_path('framework/sessions');
        if (File::exists($sessionPath)) {
            $files = File::files($sessionPath);
            foreach ($files as $file) {
                File::delete($file->getPathname());
            }
            $this->info('✅ Sesiones de archivos limpiadas');
        }
        
        // Limpiar cache de sesiones
        $this->call('cache:clear');
        $this->call('config:clear');
        $this->call('session:table');
        
        $this->info('✅ Cache y configuración limpiados');
        $this->info('🎯 Las sesiones ahora funcionarán en subdominios');
        
        return Command::SUCCESS;
    }
}
