<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;

class TestSessionSharing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'session:test-sharing';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test session sharing configuration between domain and subdomains';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🧪 Probando configuración de sesiones...');
        
        // Mostrar configuración actual
        $this->info('📋 Configuración actual:');
        $this->line('APP_URL: ' . config('app.url'));
        $this->line('SESSION_DOMAIN: ' . config('session.domain'));
        $this->line('SESSION_COOKIE: ' . config('session.cookie'));
        $this->line('SESSION_PATH: ' . config('session.path'));
        $this->line('SESSION_SECURE: ' . (config('session.secure') ? 'true' : 'false'));
        $this->line('SESSION_HTTP_ONLY: ' . (config('session.http_only') ? 'true' : 'false'));
        $this->line('SESSION_SAME_SITE: ' . config('session.same_site'));
        
        // Mostrar configuración de subdominios
        $this->info('🌐 Configuración de subdominios:');
        $this->line('Local domain: ' . config('subdomain.domain.local'));
        $this->line('Local session domain: ' . config('subdomain.session.domain.local'));
        $this->line('Production domain: ' . config('subdomain.domain.production'));
        $this->line('Production session domain: ' . config('subdomain.session.domain.production'));
        
        // Verificar si la configuración es correcta
        $appUrl = config('app.url');
        $parsedUrl = parse_url($appUrl);
        $baseHost = $parsedUrl['host'] ?? 'boletos.local';
        $expectedSessionDomain = '.' . $baseHost;
        
        $this->info('✅ Verificación:');
        $this->line('Dominio base esperado: ' . $baseHost);
        $this->line('Dominio de sesión esperado: ' . $expectedSessionDomain);
        
        if (config('session.domain') === $expectedSessionDomain) {
            $this->info('✅ La configuración de sesiones está correcta');
        } else {
            $this->error('❌ La configuración de sesiones no es correcta');
            $this->line('Esperado: ' . $expectedSessionDomain);
            $this->line('Actual: ' . config('session.domain'));
        }
        
        $this->info('🎯 Para probar:');
        $this->line('1. Inicia sesión en el dominio principal');
        $this->line('2. Navega a un subdominio');
        $this->line('3. Verifica que la sesión se mantiene');
        
        return Command::SUCCESS;
    }
}
