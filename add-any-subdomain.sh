#!/bin/bash

# Script para agregar cualquier subdominio
echo "🔧 Agregador de Subdominios para Boletos Local"
echo "=============================================="

# Verificar si se proporcionó un argumento
if [ $# -eq 0 ]; then
    echo "💡 Uso: ./add-any-subdomain.sh nombre-subdominio"
    echo ""
    echo "📝 Ejemplos:"
    echo "   ./add-any-subdomain.sh mi-evento"
    echo "   ./add-any-subdomain.sh fiesta-2024"
    echo "   ./add-any-subdomain.sh conferencia-tech"
    echo ""
    echo "🌐 Resultado: http://nombre-subdominio.boletos.local"
    exit 1
fi

SUBDOMAIN=$1
FULL_DOMAIN="${SUBDOMAIN}.boletos.test"

echo "🔍 Verificando subdominio: $FULL_DOMAIN"

# Verificar si ya existe
if grep -q "$FULL_DOMAIN" /etc/hosts; then
    echo "✅ El subdominio ya existe en hosts"
else
    echo "📝 Agregando al archivo hosts..."
    echo "127.0.0.1 $FULL_DOMAIN" | sudo tee -a /etc/hosts
    echo "✅ Subdominio agregado!"
fi

echo ""
echo "🌐 URL disponible: http://$FULL_DOMAIN"
echo ""
echo "💡 Para ver todos los subdominios configurados:"
echo "   grep 'boletos.local' /etc/hosts"
