#!/bin/bash

# Script para poblar la base de datos con datos de demostración
# Uso: ./scripts/seed-demo-data.sh

set -e

echo "🌱 Poblando base de datos con datos de demostración..."

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

# Verificar que el archivo .env existe
if [ ! -f ".env" ]; then
    error "No se encontró el archivo .env. Ejecuta primero ./scripts/setup-dev.sh"
fi

# Verificar conexión a la base de datos
log "Verificando conexión a la base de datos..."
php artisan migrate:status > /dev/null 2>&1

if [ $? -eq 0 ]; then
    log "Conexión a base de datos exitosa ✓"
else
    error "No se pudo conectar a la base de datos. Verifica la configuración en .env"
fi

# Limpiar datos existentes (opcional)
info "¿Deseas limpiar los datos existentes antes de poblar? (y/n)"
read -r CLEAR_DATA

if [ "$CLEAR_DATA" = "y" ] || [ "$CLEAR_DATA" = "Y" ]; then
    log "Limpiando datos existentes..."
    php artisan migrate:fresh --force
    log "Datos limpiados ✓"
fi

# Ejecutar seeders principales
log "Ejecutando seeders principales..."
php artisan db:seed --force

if [ $? -eq 0 ]; then
    log "Seeders principales ejecutados ✓"
else
    error "Error al ejecutar seeders principales"
fi

# Crear datos adicionales de demostración
log "Creando datos adicionales de demostración..."

# Crear eventos adicionales
log "Creando eventos adicionales..."
php artisan tinker --execute="
use App\Models\Event;
use App\Models\TicketType;

// Evento 1: Nails Awards 2024
\$event1 = Event::create([
    'name' => 'Nails Awards 2024',
    'description' => 'La ceremonia de premios más importante de la industria de uñas. Reconocemos a los mejores artistas, salones y productos del año.',
    'date' => '2024-12-15',
    'time' => '19:00:00',
    'location' => 'Centro de Convenciones CDMX',
    'status' => 'active'
]);

// Tipos de boletos para Nails Awards
TicketType::create([
    'event_id' => \$event1->id,
    'name' => 'General',
    'price' => 500.00,
    'available_quantity' => 200,
    'description' => 'Entrada general con acceso a la ceremonia'
]);

TicketType::create([
    'event_id' => \$event1->id,
    'name' => 'VIP',
    'price' => 1200.00,
    'available_quantity' => 50,
    'description' => 'Entrada VIP con cena, barra libre y meet & greet'
]);

TicketType::create([
    'event_id' => \$event1->id,
    'name' => 'Premium',
    'price' => 2000.00,
    'available_quantity' => 25,
    'description' => 'Entrada Premium con cena, barra libre, meet & greet y gift bag'
]);

// Evento 2: Workshop de Técnicas Avanzadas
\$event2 = Event::create([
    'name' => 'Workshop de Técnicas Avanzadas',
    'description' => 'Taller intensivo de técnicas avanzadas de nail art con los mejores artistas internacionales.',
    'date' => '2024-11-20',
    'time' => '09:00:00',
    'location' => 'Hotel Marriott Reforma',
    'status' => 'active'
]);

TicketType::create([
    'event_id' => \$event2->id,
    'name' => 'Estudiante',
    'price' => 800.00,
    'available_quantity' => 30,
    'description' => 'Incluye materiales y certificado'
]);

TicketType::create([
    'event_id' => \$event2->id,
    'name' => 'Profesional',
    'price' => 1200.00,
    'available_quantity' => 20,
    'description' => 'Incluye materiales, certificado y kit de herramientas'
]);

// Evento 3: Expo de Productos
\$event3 = Event::create([
    'name' => 'Expo de Productos 2024',
    'description' => 'La exposición más grande de productos para uñas en México. Descubre las últimas tendencias y productos.',
    'date' => '2024-10-10',
    'time' => '10:00:00',
    'location' => 'Expo Santa Fe',
    'status' => 'active'
]);

TicketType::create([
    'event_id' => \$event3->id,
    'name' => 'Entrada General',
    'price' => 150.00,
    'available_quantity' => 500,
    'description' => 'Acceso a todas las áreas de exhibición'
]);

TicketType::create([
    'event_id' => \$event3->id,
    'name' => 'Profesional',
    'price' => 300.00,
    'available_quantity' => 100,
    'description' => 'Acceso VIP con descuentos especiales y área de networking'
]);

echo 'Eventos y tipos de boletos creados exitosamente';
"

# Crear cupones adicionales
log "Creando cupones adicionales..."
php artisan tinker --execute="
use App\Models\Coupon;

// Cupón de descuento por porcentaje
Coupon::create([
    'code' => 'EARLYBIRD20',
    'discount_type' => 'percentage',
    'discount_value' => 20,
    'min_order_amount' => 1000.00,
    'max_discount_amount' => 500.00,
    'usage_limit' => 50,
    'used_count' => 0,
    'expires_at' => '2024-12-01 23:59:59',
    'is_active' => true
]);

// Cupón de descuento fijo
Coupon::create([
    'code' => 'SAVE100',
    'discount_type' => 'fixed',
    'discount_value' => 100.00,
    'min_order_amount' => 500.00,
    'max_discount_amount' => 100.00,
    'usage_limit' => 100,
    'used_count' => 0,
    'expires_at' => '2024-12-31 23:59:59',
    'is_active' => true
]);

// Cupón VIP
Coupon::create([
    'code' => 'VIP50',
    'discount_type' => 'percentage',
    'discount_value' => 50,
    'min_order_amount' => 2000.00,
    'max_discount_amount' => 1000.00,
    'usage_limit' => 10,
    'used_count' => 0,
    'expires_at' => '2024-11-30 23:59:59',
    'is_active' => true
]);

echo 'Cupones creados exitosamente';
"

# Crear órdenes de ejemplo
log "Creando órdenes de ejemplo..."
php artisan tinker --execute="
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Ticket;
use App\Models\Event;
use App\Models\TicketType;
use App\Models\Coupon;

// Obtener eventos y tipos de boletos
\$event1 = Event::where('name', 'Nails Awards 2024')->first();
\$event2 = Event::where('name', 'Workshop de Técnicas Avanzadas')->first();
\$event3 = Event::where('name', 'Expo de Productos 2024')->first();

\$generalTicket = TicketType::where('name', 'General')->first();
\$vipTicket = TicketType::where('name', 'VIP')->first();
\$premiumTicket = TicketType::where('name', 'Premium')->first();
\$estudianteTicket = TicketType::where('name', 'Estudiante')->first();
\$profesionalTicket = TicketType::where('name', 'Profesional')->first();
\$entradaGeneralTicket = TicketType::where('name', 'Entrada General')->first();

\$coupon = Coupon::where('code', 'EARLYBIRD20')->first();

// Orden 1: Nails Awards con cupón
\$order1 = Order::create([
    'uuid' => \Illuminate\Support\Str::uuid(),
    'event_id' => \$event1->id,
    'customer_name' => 'María González',
    'customer_email' => 'maria.gonzalez@email.com',
    'customer_phone' => '+52 55 1234 5678',
    'status' => 'completed',
    'subtotal' => 2000.00,
    'discount_amount' => 400.00,
    'tax_amount' => 256.00,
    'total' => 1856.00
]);

OrderItem::create([
    'order_id' => \$order1->id,
    'ticket_type_id' => \$vipTicket->id,
    'quantity' => 1,
    'unit_price' => 1200.00,
    'subtotal' => 1200.00
]);

OrderItem::create([
    'order_id' => \$order1->id,
    'ticket_type_id' => \$generalTicket->id,
    'quantity' => 2,
    'unit_price' => 500.00,
    'subtotal' => 1000.00
]);

// Crear tickets para la orden 1
Ticket::create([
    'order_id' => \$order1->id,
    'ticket_type_id' => \$vipTicket->id,
    'seat_number' => 'VIP-001',
    'qr_code' => 'ticket_' . \$order1->id . '_1.png',
    'status' => 'active'
]);

Ticket::create([
    'order_id' => \$order1->id,
    'ticket_type_id' => \$generalTicket->id,
    'seat_number' => 'A-001',
    'qr_code' => 'ticket_' . \$order1->id . '_2.png',
    'status' => 'active'
]);

Ticket::create([
    'order_id' => \$order1->id,
    'ticket_type_id' => \$generalTicket->id,
    'seat_number' => 'A-002',
    'qr_code' => 'ticket_' . \$order1->id . '_3.png',
    'status' => 'active'
]);

// Orden 2: Workshop
\$order2 = Order::create([
    'uuid' => \Illuminate\Support\Str::uuid(),
    'event_id' => \$event2->id,
    'customer_name' => 'Ana Rodríguez',
    'customer_email' => 'ana.rodriguez@email.com',
    'customer_phone' => '+52 55 9876 5432',
    'status' => 'completed',
    'subtotal' => 1200.00,
    'discount_amount' => 0.00,
    'tax_amount' => 192.00,
    'total' => 1392.00
]);

OrderItem::create([
    'order_id' => \$order2->id,
    'ticket_type_id' => \$profesionalTicket->id,
    'quantity' => 1,
    'unit_price' => 1200.00,
    'subtotal' => 1200.00
]);

Ticket::create([
    'order_id' => \$order2->id,
    'ticket_type_id' => \$profesionalTicket->id,
    'seat_number' => 'P-001',
    'qr_code' => 'ticket_' . \$order2->id . '_1.png',
    'status' => 'active'
]);

// Orden 3: Expo con cupón
\$order3 = Order::create([
    'uuid' => \Illuminate\Support\Str::uuid(),
    'event_id' => \$event3->id,
    'customer_name' => 'Carlos López',
    'customer_email' => 'carlos.lopez@email.com',
    'customer_phone' => '+52 55 5555 5555',
    'status' => 'pending',
    'subtotal' => 600.00,
    'discount_amount' => 100.00,
    'tax_amount' => 80.00,
    'total' => 580.00
]);

OrderItem::create([
    'order_id' => \$order3->id,
    'ticket_type_id' => \$entradaGeneralTicket->id,
    'quantity' => 4,
    'unit_price' => 150.00,
    'subtotal' => 600.00
]);

Ticket::create([
    'order_id' => \$order3->id,
    'ticket_type_id' => \$entradaGeneralTicket->id,
    'seat_number' => 'E-001',
    'qr_code' => 'ticket_' . \$order3->id . '_1.png',
    'status' => 'active'
]);

Ticket::create([
    'order_id' => \$order3->id,
    'ticket_type_id' => \$entradaGeneralTicket->id,
    'seat_number' => 'E-002',
    'qr_code' => 'ticket_' . \$order3->id . '_2.png',
    'status' => 'active'
]);

Ticket::create([
    'order_id' => \$order3->id,
    'ticket_type_id' => \$entradaGeneralTicket->id,
    'seat_number' => 'E-003',
    'qr_code' => 'ticket_' . \$order3->id . '_3.png',
    'status' => 'active'
]);

Ticket::create([
    'order_id' => \$order3->id,
    'ticket_type_id' => \$entradaGeneralTicket->id,
    'seat_number' => 'E-004',
    'qr_code' => 'ticket_' . \$order3->id . '_4.png',
    'status' => 'active'
]);

echo 'Órdenes y tickets de ejemplo creados exitosamente';
"

# Crear algunos check-ins de ejemplo
log "Creando check-ins de ejemplo..."
php artisan tinker --execute="
use App\Models\Checkin;
use App\Models\Ticket;
use App\Models\User;

// Obtener un ticket usado y un usuario staff
\$ticket = Ticket::first();
\$staffUser = User::where('email', 'staff@example.com')->first();

if (\$ticket && \$staffUser) {
    Checkin::create([
        'ticket_id' => \$ticket->id,
        'scanned_by' => \$staffUser->id,
        'scanned_at' => now()->subHours(2)
    ]);
    
    // Marcar el ticket como usado
    \$ticket->update(['status' => 'used']);
    
    echo 'Check-in de ejemplo creado exitosamente';
} else {
    echo 'No se pudo crear check-in de ejemplo';
}
"

log "🎉 Datos de demostración creados exitosamente!"
echo ""
info "📊 Resumen de datos creados:"
echo "  - 3 eventos con diferentes tipos de boletos"
echo "  - 3 cupones de descuento"
echo "  - 3 órdenes de ejemplo con tickets"
echo "  - 1 check-in de ejemplo"
echo ""
info "🎫 Eventos disponibles:"
echo "  1. Nails Awards 2024 (15 Dic 2024)"
echo "  2. Workshop de Técnicas Avanzadas (20 Nov 2024)"
echo "  3. Expo de Productos 2024 (10 Oct 2024)"
echo ""
info "🎟️ Cupones disponibles:"
echo "  - EARLYBIRD20: 20% descuento (mín. \$1000)"
echo "  - SAVE100: \$100 descuento fijo (mín. \$500)"
echo "  - VIP50: 50% descuento (mín. \$2000)"
echo ""
info "👥 Usuarios de prueba:"
echo "  - Admin: admin@example.com / password"
echo "  - Staff: staff@example.com / password"
echo "  - Viewer: viewer@example.com / password"
echo ""
info "🧪 Para probar la API:"
echo "  ./scripts/test-api.sh"
echo ""
info "🌐 Para iniciar el servidor:"
echo "  php artisan serve"
