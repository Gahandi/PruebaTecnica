<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class ActivityLog extends Model
{
    /**
     * Usar conexión de base de datos dedicada para logs
     */
    protected $connection = 'activity_log';

    const UPDATED_AT = null; // Solo usamos created_at

    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id',
        'description',
        'ip_address',
        'user_agent',
        'properties',
        'old_values',
        'new_values',
        'country',
        'city',
        'severity'
    ];

    protected $casts = [
        'properties' => 'array',
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime'
    ];

    /**
     * Traducciones de acciones en español
     */
    public static $actionTranslations = [
        'created' => 'Creado',
        'updated' => 'Actualizado',
        'deleted' => 'Eliminado',
        'restored' => 'Restaurado',
        'login' => 'Inicio de sesión',
        'logout' => 'Cierre de sesión',
        'login_failed' => 'Inicio de sesión fallido',
        'purchased' => 'Compra realizada',
        'payment_completed' => 'Pago completado',
        'payment_failed' => 'Pago fallido',
        'payment_refunded' => 'Pago reembolsado',
        'checkin' => 'Check-in realizado',
        'invited' => 'Invitación enviada',
        'invitation_accepted' => 'Invitación aceptada',
        'invitation_cancelled' => 'Invitación cancelada',
        'exported' => 'Datos exportados',
        'imported' => 'Datos importados',
        'password_reset' => 'Contraseña restablecida',
        'password_changed' => 'Contraseña cambiada',
        'email_verified' => 'Email verificado',
        'profile_updated' => 'Perfil actualizado',
        'role_changed' => 'Rol cambiado',
        'permission_changed' => 'Permisos modificados',
        'ticket_transferred' => 'Boleto transferido',
        'ticket_cancelled' => 'Boleto cancelado',
        'event_published' => 'Evento publicado',
        'event_unpublished' => 'Evento despublicado',
        'coupon_used' => 'Cupón utilizado',
        'coupon_created' => 'Cupón creado',
        'follow' => 'Siguiendo espacio',
        'unfollow' => 'Dejó de seguir',
        'viewed' => 'Visualizado',
    ];

    /**
     * Traducciones de modelos en español
     */
    public static $modelTranslations = [
        'User' => 'Usuario',
        'Event' => 'Evento',
        'Order' => 'Orden',
        'Ticket' => 'Boleto',
        'Payment' => 'Pago',
        'Space' => 'Espacio',
        'TicketType' => 'Tipo de Boleto',
        'Coupon' => 'Cupón',
        'SpaceInvitation' => 'Invitación',
        'Checkin' => 'Check-in',
        'Tag' => 'Etiqueta',
        'TypeEvent' => 'Categoría',
        'Permission' => 'Permiso',
        'Role' => 'Rol',
        'RoleSpace' => 'Rol de Espacio',
        'Setting' => 'Configuración',
    ];

    /**
     * Colores para cada acción
     */
    public static $actionColors = [
        'created' => 'green',
        'updated' => 'blue',
        'deleted' => 'red',
        'restored' => 'teal',
        'login' => 'indigo',
        'logout' => 'gray',
        'login_failed' => 'red',
        'purchased' => 'green',
        'payment_completed' => 'green',
        'payment_failed' => 'red',
        'payment_refunded' => 'orange',
        'checkin' => 'purple',
        'invited' => 'blue',
        'invitation_accepted' => 'green',
        'invitation_cancelled' => 'red',
        'exported' => 'yellow',
        'imported' => 'yellow',
        'password_reset' => 'orange',
        'password_changed' => 'orange',
        'email_verified' => 'green',
        'profile_updated' => 'blue',
        'role_changed' => 'purple',
        'permission_changed' => 'purple',
        'ticket_transferred' => 'blue',
        'ticket_cancelled' => 'red',
        'event_published' => 'green',
        'event_unpublished' => 'orange',
        'coupon_used' => 'green',
        'coupon_created' => 'blue',
        'follow' => 'pink',
        'unfollow' => 'gray',
        'viewed' => 'gray',
    ];

    /**
     * Niveles de severidad
     */
    const SEVERITY_INFO = 'info';
    const SEVERITY_WARNING = 'warning';
    const SEVERITY_ERROR = 'error';
    const SEVERITY_CRITICAL = 'critical';

    /**
     * Relación con el usuario que realizó la acción
     */
    public function user()
    {
        return $this->setConnection('mysql')->belongsTo(User::class);
    }

    /**
     * Registrar una actividad
     */
    public static function log($action, $description, $modelType = null, $modelId = null, $properties = [], $oldValues = null, $newValues = null)
    {
        // Obtener información de ubicación desde IP
        $ip = Request::ip();
        $location = self::getLocationFromIp($ip);

        return self::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => $description,
            'ip_address' => $ip,
            'user_agent' => Request::userAgent(),
            'properties' => $properties,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'country' => $location['country'] ?? null,
            'city' => $location['city'] ?? null,
            'severity' => self::SEVERITY_INFO
        ]);
    }

    /**
     * Registrar con severidad específica
     */
    public static function logWithSeverity($action, $description, $severity, $modelType = null, $modelId = null, $properties = [], $oldValues = null, $newValues = null)
    {
        $log = self::log($action, $description, $modelType, $modelId, $properties, $oldValues, $newValues);
        $log->update(['severity' => $severity]);
        return $log;
    }

    /**
     * Obtener ubicación desde IP (simplificado, puede mejorarse con servicio externo)
     */
    protected static function getLocationFromIp($ip)
    {
        // Si es localhost, retornar valores por defecto
        if (in_array($ip, ['127.0.0.1', '::1', 'localhost'])) {
            return ['country' => 'Local', 'city' => 'Desarrollo'];
        }

        // Para producción, se puede integrar con un servicio como ip-api.com o maxmind
        // Por ahora retornamos null y se puede implementar después
        try {
            // Ejemplo básico con ip-api.com (gratuito, 45 req/min)
            // $response = file_get_contents("http://ip-api.com/json/{$ip}?lang=es");
            // $data = json_decode($response, true);
            // return ['country' => $data['country'] ?? null, 'city' => $data['city'] ?? null];
            return ['country' => null, 'city' => null];
        } catch (\Exception $e) {
            return ['country' => null, 'city' => null];
        }
    }

    /**
     * Obtener la acción traducida
     */
    public function getActionTranslatedAttribute()
    {
        return self::$actionTranslations[$this->action] ?? ucfirst($this->action);
    }

    /**
     * Obtener el modelo traducido
     */
    public function getModelTranslatedAttribute()
    {
        if (!$this->model_type)
            return null;
        $shortName = class_basename($this->model_type);
        return self::$modelTranslations[$shortName] ?? $shortName;
    }

    /**
     * Obtener el color según la acción
     */
    public function getColorAttribute()
    {
        return self::$actionColors[$this->action] ?? 'gray';
    }

    /**
     * Scope para filtrar por acción
     */
    public function scopeAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope para filtrar por usuario
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para filtrar por modelo
     */
    public function scopeForModel($query, $modelType, $modelId = null)
    {
        $query->where('model_type', $modelType);

        if ($modelId) {
            $query->where('model_id', $modelId);
        }

        return $query;
    }

    /**
     * Scope para actividades recientes
     */
    public function scopeRecent($query, $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope para filtrar por severidad
     */
    public function scopeBySeverity($query, $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Obtener cambios formateados para mostrar
     */
    public function getFormattedChangesAttribute()
    {
        if (!$this->old_values && !$this->new_values) {
            return null;
        }

        $changes = [];
        $oldValues = $this->old_values ?? [];
        $newValues = $this->new_values ?? [];

        // Obtener todas las claves modificadas
        $allKeys = array_unique(array_merge(array_keys($oldValues), array_keys($newValues)));

        foreach ($allKeys as $key) {
            $old = $oldValues[$key] ?? null;
            $new = $newValues[$key] ?? null;

            if ($old !== $new) {
                $changes[] = [
                    'field' => $this->translateFieldName($key),
                    'old' => $this->formatValue($old),
                    'new' => $this->formatValue($new)
                ];
            }
        }

        return $changes;
    }

    /**
     * Traducir nombre de campo
     */
    protected function translateFieldName($field)
    {
        $translations = [
            'name' => 'Nombre',
            'email' => 'Correo electrónico',
            'password' => 'Contraseña',
            'phone' => 'Teléfono',
            'address' => 'Dirección',
            'description' => 'Descripción',
            'price' => 'Precio',
            'quantity' => 'Cantidad',
            'status' => 'Estado',
            'active' => 'Activo',
            'date' => 'Fecha',
            'start_date' => 'Fecha de inicio',
            'end_date' => 'Fecha de fin',
            'created_at' => 'Creado',
            'updated_at' => 'Actualizado',
            'deleted_at' => 'Eliminado',
            'role' => 'Rol',
            'role_space_id' => 'Rol en espacio',
            'user_id' => 'Usuario',
            'event_id' => 'Evento',
            'space_id' => 'Espacio',
            'total' => 'Total',
            'subtotal' => 'Subtotal',
            'discount' => 'Descuento',
            'last_name' => 'Apellido',
            'image' => 'Imagen',
            'banner' => 'Banner',
            'logo' => 'Logo',
            'subdomain' => 'Subdominio',
            'location' => 'Ubicación',
            'website' => 'Sitio web',
        ];

        return $translations[$field] ?? ucfirst(str_replace('_', ' ', $field));
    }

    /**
     * Formatear valor para mostrar
     */
    protected function formatValue($value)
    {
        if (is_null($value))
            return '(vacío)';
        if (is_bool($value))
            return $value ? 'Sí' : 'No';
        if (is_array($value))
            return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (strlen($value) > 100)
            return substr($value, 0, 100) . '...';
        return $value;
    }
}
