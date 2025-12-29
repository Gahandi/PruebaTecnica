<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Display settings dashboard
     */
    public function index()
    {
        $settings = Setting::orderBy('group')->orderBy('key')->get()->groupBy('group');

        ActivityLog::log('viewed', 'Viewed settings page');

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'nullable',
        ]);

        foreach ($validated['settings'] as $key => $value) {
            $setting = Setting::where('key', $key)->first();

            if ($setting) {
                $setting->update([
                    'value' => is_array($value) ? json_encode($value) : $value
                ]);
            }
        }

        ActivityLog::log('updated', 'Updated system settings', null, null, [
            'settings_count' => count($validated['settings'])
        ]);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Configuración actualizada exitosamente.');
    }

    /**
     * Create new setting
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'key' => 'required|string|unique:settings,key',
            'value' => 'nullable',
            'type' => 'required|in:string,boolean,integer,json',
            'group' => 'required|string',
            'description' => 'nullable|string',
        ]);

        Setting::create($validated);

        ActivityLog::log('created', "Created setting: {$validated['key']}", 'Setting', null, $validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Configuración creada exitosamente.');
    }

    /**
     * Delete setting
     */
    public function destroy(Setting $setting)
    {
        $key = $setting->key;
        $setting->delete();

        ActivityLog::log('deleted', "Deleted setting: {$key}", 'Setting', null);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Configuración eliminada exitosamente.');
    }

    /**
     * Initialize default settings
     */
    public function initializeDefaults()
    {
        $defaults = [
            // General
            ['key' => 'site_name', 'value' => 'Event Ticketing System', 'type' => 'string', 'group' => 'general', 'description' => 'Nombre del sitio'],
            ['key' => 'site_description', 'value' => 'Sistema de gestión de eventos y tickets', 'type' => 'string', 'group' => 'general', 'description' => 'Descripción del sitio'],
            ['key' => 'contact_email', 'value' => 'admin@example.com', 'type' => 'string', 'group' => 'general', 'description' => 'Email de contacto'],
            ['key' => 'timezone', 'value' => 'America/Mexico_City', 'type' => 'string', 'group' => 'general', 'description' => 'Zona horaria'],

            // Notifications
            ['key' => 'email_notifications', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Activar notificaciones por email'],
            ['key' => 'order_confirmation_email', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Email de confirmación de orden'],
            ['key' => 'checkin_notification', 'value' => 'true', 'type' => 'boolean', 'group' => 'notifications', 'description' => 'Notificación de check-in'],

            // System
            ['key' => 'maintenance_mode', 'value' => 'false', 'type' => 'boolean', 'group' => 'system', 'description' => 'Modo mantenimiento'],
            ['key' => 'max_tickets_per_order', 'value' => '10', 'type' => 'integer', 'group' => 'system', 'description' => 'Máximo de tickets por orden'],
            ['key' => 'session_timeout', 'value' => '120', 'type' => 'integer', 'group' => 'system', 'description' => 'Tiempo de sesión (minutos)'],

            // Fees
            ['key' => 'service_charge_name', 'value' => 'Cargo por servicio', 'type' => 'string', 'group' => 'fees', 'description' => 'Nombre visible del cargo en el carrito'],
            ['key' => 'service_charge_percentage', 'value' => '16', 'type' => 'integer', 'group' => 'fees', 'description' => 'Porcentaje de cargo por servicio sobre el subtotal'],
        ];

        foreach ($defaults as $default) {
            Setting::updateOrCreate(
                ['key' => $default['key']],
                $default
            );
        }

        ActivityLog::log('created', 'Initialized default settings', null, null, [
            'count' => count($defaults)
        ]);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Configuración por defecto inicializada.');
    }

    /**
     * Show create form
     */
    public function create()
    {
        return view('admin.settings.create');
    }

    /**
     * Show edit form
     */
    public function edit(Setting $setting)
    {
        return view('admin.settings.edit', compact('setting'));
    }

    /**
     * Update single setting
     */
    public function updateSingle(Request $request, Setting $setting)
    {
        $validated = $request->validate([
            'value' => 'nullable',
            'type' => 'required|in:string,boolean,integer,json',
            'group' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $setting->update($validated);

        ActivityLog::log('updated', "Updated setting: {$setting->key}", 'Setting', $setting->id, $validated);

        return redirect()->route('admin.settings.index')
            ->with('success', 'Configuración actualizada exitosamente.');
    }
}
