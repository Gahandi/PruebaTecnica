#!/bin/bash

# Script para servir Laravel en boletos.local puerto 80
echo "🚀 Iniciando servidor Laravel en boletos.local:80..."

# Verificar si ya está configurado el hosts
if ! grep -q "boletos.local" /etc/hosts; then
    echo "⚠️  Configurando archivo hosts..."
    echo "127.0.0.1 boletos.local" | sudo tee -a /etc/hosts
    echo "127.0.0.1 mi-cajon.boletos.local" | sudo tee -a /etc/hosts
    echo "127.0.0.1 prueba-de-cajon.boletos.local" | sudo tee -a /etc/hosts
    echo "✅ Hosts configurado!"
fi

# Verificar si el puerto 80 está disponible
if lsof -Pi :80 -sTCP:LISTEN -t >/dev/null ; then
    echo "⚠️  Puerto 80 está en uso. Intentando liberar..."
    echo "💡 Si tienes Apache/Nginx corriendo, deténlo primero:"
    echo "   sudo brew services stop httpd"
    echo "   sudo brew services stop nginx"
    echo ""
    echo "🔄 O usa un puerto diferente:"
    echo "   php artisan serve --host=boletos.local --port=8080"
    exit 1
fi

echo "✅ Puerto 80 disponible!"
echo "🌐 Servidor iniciado en: http://boletos.local"
echo "📱 Subdominios disponibles:"
echo "   - http://mi-cajon.boletos.local"
echo "   - http://prueba-de-cajon.boletos.local"
echo ""
echo "🛑 Para detener: Ctrl+C"
echo ""

# Iniciar servidor Laravel en puerto 80
php artisan serve --host=boletos.local --port=8080
