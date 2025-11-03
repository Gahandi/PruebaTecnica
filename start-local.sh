#!/bin/bash

# Script de inicio rápido para boletos.local
echo "🚀 Iniciando Boletos Local Development Server"
echo "=============================================="

# Verificar si ya está configurado
if ! grep -q "boletos.local" /etc/hosts; then
    echo "📝 Configurando archivo hosts..."
    echo "127.0.0.1 boletos.local" | sudo tee -a /etc/hosts
    echo "127.0.0.1 mi-cajon.boletos.local" | sudo tee -a /etc/hosts
    echo "127.0.0.1 prueba-de-cajon.boletos.local" | sudo tee -a /etc/hosts
    echo "127.0.0.1 prueba-de-cajon-2.boletos.local" | sudo tee -a /etc/hosts
    echo "✅ Hosts configurado!"
else
    echo "✅ Hosts ya configurado!"
    echo "📋 Subdominios disponibles:"
    grep "boletos.local" /etc/hosts | grep -o '[a-zA-Z0-9-]*\.boletos\.local' | while read subdomain; do
        echo "   ✅ http://$subdomain"
    done
fi

# Verificar puerto 80
echo "🔍 Verificando puerto 80..."
if lsof -Pi :80 -sTCP:LISTEN -t >/dev/null 2>&1; then
    echo "⚠️  Puerto 80 está en uso!"
    echo "💡 Soluciones:"
    echo "   1. Detener Apache: sudo brew services stop httpd"
    echo "   2. Detener Nginx: sudo brew services stop nginx"
    echo "   3. Usar puerto alternativo: php artisan serve --host=boletos.local --port=8080"
    echo ""
    read -p "¿Continuar con puerto 8080? (y/n): " -n 1 -r
    echo
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "🌐 Iniciando en puerto 8080..."
        php artisan serve --host=boletos.local --port=8080
    else
        echo "❌ Operación cancelada"
        exit 1
    fi
else
    echo "✅ Puerto 80 disponible!"
    echo "🌐 Iniciando servidor en http://boletos.local"
    echo ""
    echo "📱 URLs disponibles:"
    echo "   - Principal: http://boletos.local"
    echo "   - Cajón 1: http://mi-cajon.boletos.local"
    echo "   - Cajón 2: http://prueba-de-cajon.boletos.local"
    echo ""
    echo "🛑 Para detener: Ctrl+C"
    echo ""
    php artisan serve --host=boletos.local --port=80
fi
