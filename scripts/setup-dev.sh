#!/bin/bash

# Script de configuración para desarrollo local
# Uso: ./scripts/setup-dev.sh

set -e

echo "🛠️  Configurando entorno de desarrollo..."

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para logging
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}"
    exit 1
}

warning() {
    echo -e "${YELLOW}[WARNING] $1${NC}"
}

info() {
    echo -e "${BLUE}[INFO] $1${NC}"
}

# Verificar que estamos en el directorio correcto
if [ ! -f "artisan" ]; then
    error "No se encontró el archivo artisan. Asegúrate de estar en el directorio raíz del proyecto Laravel."
fi

# Verificar PHP
if ! command -v php &> /dev/null; then
    error "PHP no está instalado. Por favor instala PHP 8.2+ antes de continuar."
fi

PHP_VERSION=$(php -r "echo PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;")
if [ "$(echo "$PHP_VERSION < 8.2" | bc -l)" -eq 1 ]; then
    error "Se requiere PHP 8.2 o superior. Versión actual: $PHP_VERSION"
fi

log "PHP $PHP_VERSION detectado ✓"

# Verificar Composer
if ! command -v composer &> /dev/null; then
    error "Composer no está instalado. Por favor instala Composer antes de continuar."
fi

log "Composer detectado ✓"

# Verificar Node.js
if ! command -v node &> /dev/null; then
    error "Node.js no está instalado. Por favor instala Node.js 18+ antes de continuar."
fi

NODE_VERSION=$(node -v | cut -d'v' -f2 | cut -d'.' -f1)
if [ "$NODE_VERSION" -lt 18 ]; then
    error "Se requiere Node.js 18 o superior. Versión actual: v$(node -v | cut -d'v' -f2)"
fi

log "Node.js $(node -v) detectado ✓"

# Verificar Docker (opcional)
if command -v docker &> /dev/null; then
    log "Docker detectado ✓"
    DOCKER_AVAILABLE=true
else
    warning "Docker no está disponible. Algunas funcionalidades pueden no estar disponibles."
    DOCKER_AVAILABLE=false
fi

# Crear archivo .env si no existe
if [ ! -f ".env" ]; then
    log "Creando archivo .env desde .env.example..."
    if [ -f ".env.example" ]; then
        cp .env.example .env
        log "Archivo .env creado ✓"
    else
        error "No se encontró .env.example. Crea un archivo .env con la configuración necesaria."
    fi
else
    log "Archivo .env ya existe ✓"
fi

# Configurar .env para desarrollo
log "Configurando variables de entorno para desarrollo..."
sed -i.bak 's/APP_DEBUG=false/APP_DEBUG=true/' .env
sed -i.bak 's/APP_ENV=production/APP_ENV=local/' .env
sed -i.bak 's/LOG_LEVEL=error/LOG_LEVEL=debug/' .env

# Generar clave de aplicación si no existe
if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env; then
    log "Generando clave de aplicación..."
    php artisan key:generate
    log "Clave de aplicación generada ✓"
fi

# Instalar dependencias PHP
log "Instalando dependencias PHP..."
composer install

if [ $? -eq 0 ]; then
    log "Dependencias PHP instaladas ✓"
else
    error "Error al instalar dependencias PHP"
fi

# Instalar dependencias Node.js
log "Instalando dependencias Node.js..."
npm install

if [ $? -eq 0 ]; then
    log "Dependencias Node.js instaladas ✓"
else
    error "Error al instalar dependencias Node.js"
fi

# Compilar assets
log "Compilando assets..."
npm run build

if [ $? -eq 0 ]; then
    log "Assets compilados ✓"
else
    error "Error al compilar assets"
fi

# Configurar base de datos
log "Configurando base de datos..."

# Verificar si se usa Docker para la base de datos
if [ "$DOCKER_AVAILABLE" = true ]; then
    info "¿Deseas usar Docker para la base de datos? (y/n)"
    read -r USE_DOCKER_DB
    
    if [ "$USE_DOCKER_DB" = "y" ] || [ "$USE_DOCKER_DB" = "Y" ]; then
        log "Iniciando servicios con Docker Compose..."
        docker-compose up -d db redis
        
        # Esperar a que la base de datos esté lista
        log "Esperando a que la base de datos esté lista..."
        sleep 10
        
        # Configurar .env para Docker
        sed -i.bak 's/DB_HOST=127.0.0.1/DB_HOST=db/' .env
        sed -i.bak 's/REDIS_HOST=127.0.0.1/REDIS_HOST=redis/' .env
        sed -i.bak 's/DB_DATABASE=laravel/DB_DATABASE=ticket_system/' .env
        sed -i.bak 's/DB_USERNAME=root/DB_USERNAME=ticket_user/' .env
        sed -i.bak 's/DB_PASSWORD=/DB_PASSWORD=ticket_password/' .env
        
        log "Configuración de Docker aplicada ✓"
    fi
fi

# Ejecutar migraciones
log "Ejecutando migraciones de base de datos..."
php artisan migrate

if [ $? -eq 0 ]; then
    log "Migraciones ejecutadas ✓"
else
    error "Error al ejecutar migraciones. Verifica la configuración de la base de datos."
fi

# Ejecutar seeders
log "Ejecutando seeders de base de datos..."
php artisan db:seed

if [ $? -eq 0 ]; then
    log "Seeders ejecutados ✓"
else
    warning "Error al ejecutar seeders (continuando...)"
fi

# Crear enlace simbólico para storage
log "Creando enlace simbólico para storage..."
php artisan storage:link

# Ejecutar tests
if [ -d "tests" ]; then
    log "Ejecutando tests..."
    php artisan test
    
    if [ $? -eq 0 ]; then
        log "Tests ejecutados exitosamente ✓"
    else
        warning "Algunos tests fallaron"
    fi
fi

# Limpiar archivos temporales
rm -f .env.bak

echo ""
log "🎉 Configuración de desarrollo completada!"
echo ""
info "📋 Información de acceso:"
echo "  🌐 Aplicación: http://localhost:8000"
echo "  👤 Admin: admin@example.com / password"
echo "  👤 Staff: staff@example.com / password"
echo "  👤 Viewer: viewer@example.com / password"
echo ""
info "🚀 Comandos útiles:"
echo "  Iniciar servidor: php artisan serve"
echo "  Ver logs: tail -f storage/logs/laravel.log"
echo "  Ejecutar tests: php artisan test"
echo "  Limpiar cache: php artisan cache:clear"
echo ""
if [ "$DOCKER_AVAILABLE" = true ]; then
    info "🐳 Comandos Docker:"
    echo "  Iniciar todos los servicios: docker-compose up -d"
    echo "  Ver logs: docker-compose logs -f"
    echo "  Detener servicios: docker-compose down"
fi
echo ""
info "📚 Documentación:"
echo "  API: docs/API.md"
echo "  Modelo de datos: docs/DATABASE_MODEL.md"
echo "  README: README.md"
