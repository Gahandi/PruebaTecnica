# Sistema de Venta de Boletos - Laravel

Una aplicación completa de venta de boletos para eventos con autenticación por roles, API pública, checkout simulado y panel de administración.

## Características

### Funcionalidades Principales
- Autenticación con roles: admin, staff, viewer
- CRUD completo: eventos, tipos de boletos, cupones
- Checkout simulado: generación de órdenes y tickets con QR
- Panel de administración: gestión completa del sistema
- Check-in con validación QR: para personal de staff
- API pública: endpoints para consulta y compra
- Dashboard con métricas: ingresos, boletos vendidos, cupones usados
- Docker: Configuración completa para desarrollo y producción
- Despliegue en Render: Configuración lista para PaaS

### Requisitos Técnicos
- Laravel 10+
- PHP 8.2+
- MySQL/PostgreSQL
- Redis (opcional)
- Composer
- Docker (para desarrollo)
- Node.js 18+ (para assets)

## Instalación

### Opción 1: Desarrollo Local (Recomendado)

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd PruebaTecnica
```

2. **Configuración automática**
```bash
# Ejecutar script de configuración
./scripts/setup-dev.sh
```

3. **Poblar con datos de demostración**
```bash
# Crear datos de ejemplo
./scripts/seed-demo-data.sh
```

4. **Iniciar servidor**
```bash
php artisan serve
```

### Opción 2: Con Docker

1. **Clonar el repositorio**
```bash
git clone <repository-url>
cd PruebaTecnica
```

2. **Configurar variables de entorno**
```bash
cp env.example .env
# Editar .env con la configuración necesaria
```

3. **Iniciar servicios**
```bash
docker-compose up -d
```

4. **Ejecutar migraciones y seeders**
```bash
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed
```

5. **Acceder a la aplicación**
```
http://localhost:8000
```

## Usuarios de Prueba

El seeder crea los siguientes usuarios:

- **Admin**: admin@example.com / password
- **Staff**: staff@example.com / password  
- **Viewer**: viewer@example.com / password

## Estructura del Proyecto

### Modelos
- `User`: Usuarios del sistema con roles
- `Event`: Eventos disponibles
- `TicketType`: Tipos de boletos por evento
- `Coupon`: Cupones de descuento
- `Order`: Órdenes de compra
- `OrderItem`: Items de cada orden
- `Ticket`: Tickets individuales con QR
- `Checkin`: Registro de check-ins

### Controladores

#### Admin (Solo admin)
- `EventController`: CRUD de eventos
- `TicketTypeController`: CRUD de tipos de boletos
- `CouponController`: CRUD de cupones
- `OrderController`: Visualización de órdenes

#### Staff (Solo staff)
- `CheckinController`: Validación de tickets

#### API Pública
- `Api\EventController`: Consulta de eventos
- `Api\OrderController`: Creación de órdenes
- `Api\TicketController`: Validación de tickets

### Middlewares
- `CheckRole`: Validación de roles de usuario
- `ApiSecurity`: Headers de seguridad para API

## API Endpoints

### Eventos
- `GET /api/v1/events` - Listar todos los eventos
- `GET /api/v1/events/{id}` - Obtener evento específico

### Órdenes
- `POST /api/v1/orders` - Crear nueva orden
- `GET /api/v1/orders/{id}` - Obtener orden específica

### Tickets
- `GET /api/v1/tickets/{id}` - Obtener ticket específico
- `GET /api/v1/tickets/{id}/validate` - Validar ticket

### Ejemplo de Creación de Orden
```json
POST /api/v1/orders
{
    "event_id": 1,
    "tickets": [
        {
            "ticket_type_id": 1,
            "quantity": 2
        }
    ],
    "coupon_code": "DESCUENTO20",
    "customer_name": "John Doe",
    "customer_email": "john@example.com"
}
```

## Seguridad

- **Rate Limiting**: 60 requests/min para consultas, 10 requests/min para órdenes
- **Headers de Seguridad**: XSS, CSRF, Clickjacking protection
- **Validación de Datos**: Validación estricta en todos los endpoints
- **Autenticación por Roles**: Middleware personalizado para control de acceso

## Dashboard

El dashboard incluye:
- Métricas generales (eventos, órdenes, tickets, ingresos)
- Gráfico de ingresos por mes
- Boletos vendidos por tipo
- Eventos más populares
- Cupones más usados
- Check-ins recientes

## Testing

### Ejecutar Tests
```bash
# Tests completos
php artisan test

# Tests específicos
php artisan test --filter OrderFlowTest

# Con cobertura
php artisan test --coverage
```

### Probar API
```bash
# Probar API local
curl -X GET "http://localhost:8000/api/v1/events"

# Probar API en producción
curl -X GET "https://prueba.gahandi.dev/api/v1/events"
```

## Scripts Disponibles

| Script | Descripción |
|--------|-------------|
| `./scripts/setup-dev.sh` | Configuración automática para desarrollo |
| `./scripts/seed-demo-data.sh` | Poblar con datos de demostración |

## Docker

### Desarrollo
```bash
# Iniciar todos los servicios
docker-compose up -d

# Ver logs
docker-compose logs -f

# Ejecutar comandos
docker-compose exec app php artisan migrate
docker-compose exec app php artisan db:seed

# Detener servicios
docker-compose down
```

### Producción
```bash
# Construir imagen
docker build -t ticket-system .

# Ejecutar contenedor
docker run -d -p 80:80 --env-file .env ticket-system

# O usar Docker Compose para producción
docker-compose -f docker-compose.prod.yml up -d
```

## URLs de Acceso

### Desarrollo Local
- **Aplicación**: http://localhost:8000
- **API**: http://localhost:8000/api/v1
- **Admin**: http://localhost:8000/admin

### Producción (Render)
- **Aplicación**: https://prueba.gahandi.dev
- **API**: https://prueba.gahandi.dev/api/v1
- **Admin**: https://prueba.gahandi.dev/admin

## Credenciales de Prueba

| Rol | Email | Contraseña | Acceso |
|-----|-------|------------|--------|
| Admin | admin@example.com | password | Completo |
| Staff | staff@example.com | password | Check-in y órdenes |
| Viewer | viewer@example.com | password | Solo consultas |

## Despliegue

### Opción 1: Render (Recomendado)

#### Configuración Automática con Aiven MySQL

1. **Fork del repositorio**
   - Haz fork de este repositorio en GitHub
   - Conecta tu cuenta de Render con GitHub

2. **Crear servicio en Render**
   - Ve a [Render Dashboard](https://dashboard.render.com)
   - Click en "New +" → "Web Service"
   - Conecta tu repositorio de GitHub
   - Selecciona la rama `main`

3. **Configurar el servicio**
   - **Name**: `ticket-system-aiven`
   - **Environment**: `Docker`
   - **Region**: `Oregon (US West)`
   - **Branch**: `main`
   - **Dockerfile Path**: `./Dockerfile`

4. **Usar archivo de configuración**
   - Render detectará automáticamente el `render-production.yaml`
   - Este archivo ya incluye todas las variables de Aiven MySQL

5. **Configurar dominio personalizado**
   - Ve a la configuración del servicio
   - Click en "Custom Domains"
   - Agrega `prueba.gahandi.dev`

6. **Configurar DNS**
   ```
   Tipo: CNAME
   Nombre: prueba
   Valor: ticket-system-aiven.onrender.com
   TTL: 300
   ```

#### Variables de Entorno Incluidas

El archivo `render-production.yaml` incluye automáticamente:

```env
# Aplicación
APP_ENV=production
APP_DEBUG=false
APP_URL=https://prueba.gahandi.dev

# Base de datos Aiven MySQL
DB_CONNECTION=mysql
DB_HOST=apps-web-extra-gsch-69b2.g.aivencloud.com
DB_PORT=20606
DB_DATABASE=defaultdb
DB_USERNAME=avnadmin
DB_PASSWORD=AVNS_5ers-mFGmvYZyuNz9zT

# SSL Configuration
MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=false
MYSQL_ATTR_SSL_CIPHER=""

# Cache y sesiones
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync

# Configuración específica del sistema
TICKET_QR_SIZE=200
TAX_RATE=16
TAX_NAME="IVA"
# ... más variables
```

#### Verificar Despliegue

```bash
# Verificar que la aplicación esté funcionando
curl -I https://prueba.gahandi.dev

# Probar la API
curl -X GET "https://prueba.gahandi.dev/api/v1/events"

# Probar login
curl -X POST "https://prueba.gahandi.dev/login" \
  -H "Content-Type: application/json" \
  -d '{"email":"admin@example.com","password":"password"}'
```

### Opción 2: Docker en VPS

1. **Preparar servidor**
   ```bash
   # Instalar Docker y Docker Compose
   curl -fsSL https://get.docker.com -o get-docker.sh
   sh get-docker.sh
   ```

2. **Clonar y configurar**
   ```bash
   git clone <repository-url>
   cd PruebaTecnica
   cp env.example .env
   # Editar .env con configuración de producción
   ```

3. **Desplegar**
   ```bash
   # Usar Docker Compose para producción
   docker-compose -f docker-compose.prod.yml up -d
   
   # O construir imagen manualmente
   docker build -t ticket-system .
   docker run -d -p 80:80 --env-file .env ticket-system
   ```

### Opción 3: Despliegue Manual

1. **Configurar servidor**
   - Instalar PHP 8.2+, MySQL, Nginx/Apache
   - Configurar SSL con Let's Encrypt
   - Configurar dominio y DNS

2. **Desplegar aplicación**
   ```bash
   git clone <repository-url>
   cd PruebaTecnica
   composer install --no-dev --optimize-autoloader
   npm install && npm run build
   php artisan migrate --force
   php artisan optimize
   ```

3. **Configurar servidor web**
   - Configurar virtual host
   - Configurar SSL/TLS
   - Configurar permisos de archivos

## Variables de Entorno Requeridas

```env
# Aplicación
APP_NAME="Sistema de Boletos"
APP_ENV=production
APP_KEY=base64:...
APP_DEBUG=false
APP_URL=https://prueba.gahandi.dev

# Base de datos
DB_CONNECTION=mysql
DB_HOST=...
DB_PORT=3306
DB_DATABASE=...
DB_USERNAME=...
DB_PASSWORD=...

# Cache y Sesiones
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
REDIS_URL=...

# Email
MAIL_MAILER=smtp
MAIL_HOST=...
MAIL_PORT=587
MAIL_USERNAME=...
MAIL_PASSWORD=...
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=...
MAIL_FROM_NAME="Sistema de Boletos"
```

## Flujo de Compra

1. Usuario consulta eventos disponibles
2. Selecciona tipos de boletos y cantidades
3. Aplica cupón de descuento (opcional)
4. Sistema calcula totales e impuestos (16% IVA)
5. Genera orden y tickets con QR
6. Envía confirmación por email

## Check-in

1. Staff escanea QR del ticket
2. Sistema valida que el ticket no haya sido usado
3. Registra check-in con timestamp
4. Marca ticket como usado

## Modelo de Datos

### Tablas Principales
- **users**: Usuarios del sistema con autenticación básica de Laravel
- **events**: Eventos disponibles para la venta de boletos
- **ticket_types**: Tipos de boletos disponibles para cada evento
- **coupons**: Cupones de descuento que pueden aplicarse a las órdenes
- **orders**: Órdenes de compra realizadas por los clientes
- **order_items**: Items individuales de cada orden
- **tickets**: Tickets individuales generados para cada boleto comprado
- **checkins**: Registro de check-ins realizados por el personal de staff

### Relaciones Principales
1. **Event → Ticket Types**: Un evento tiene muchos tipos de boletos
2. **Event → Orders**: Un evento puede tener muchas órdenes
3. **Order → Order Items**: Una orden tiene muchos items
4. **Order → Tickets**: Una orden genera muchos tickets
5. **Ticket Type → Tickets**: Un tipo de boleto puede generar muchos tickets
6. **Ticket → Checkins**: Un ticket puede tener un check-in
7. **User → Checkins**: Un usuario (staff) puede realizar muchos check-ins

### Sistema de Roles y Permisos
- **admin**: Acceso completo al sistema
- **staff**: Puede realizar check-ins y ver órdenes
- **viewer**: Solo puede consultar información

## Métricas y Monitoreo

### Dashboard de Administración
- Ingresos totales y por mes
- Boletos vendidos por tipo
- Eventos más populares
- Cupones más usados
- Check-ins recientes

### Logs
```bash
# Ver logs de la aplicación
tail -f storage/logs/laravel.log

# Logs de Docker
docker-compose logs -f app
```

## Solución de Problemas

### Problemas Comunes

1. **Error de permisos**
   ```bash
   chmod -R 755 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

2. **Error de base de datos**
   ```bash
   php artisan migrate:status
   php artisan migrate:reset
   php artisan migrate
   ```

3. **Error de cache**
   ```bash
   php artisan cache:clear
   php artisan config:clear
   php artisan route:clear
   php artisan view:clear
   ```

4. **Error de assets**
   ```bash
   npm install
   npm run build
   php artisan storage:link
   ```

## Contribución

1. Fork el proyecto
2. Crear rama para feature (`git checkout -b feature/AmazingFeature`)
3. Commit cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abrir Pull Request

## Licencia

Este proyecto está bajo la Licencia MIT. Ver `LICENSE` para más detalles.

## Autores

- **Desarrollador** - *Desarrollo inicial* - [GitHub](https://github.com/username)

## Agradecimientos

- Laravel Framework
- Tailwind CSS
- Chart.js
- QR Server API
- Spatie Laravel Permission
- Laravel DomPDF