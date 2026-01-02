<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * La migración se ejecuta en la conexión de activity_log
     */
    protected $connection = 'activity_log';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Crear la tabla si no existe (para la nueva BD)
        if (!Schema::connection('activity_log')->hasTable('activity_logs')) {
            Schema::connection('activity_log')->create('activity_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action'); // created, updated, deleted, login, logout, etc.
                $table->string('model_type')->nullable(); // User, Event, Order, etc.
                $table->string('model_id')->nullable();
                $table->text('description');
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->json('properties')->nullable(); // Additional data
                $table->json('old_values')->nullable(); // Valores anteriores
                $table->json('new_values')->nullable(); // Valores nuevos
                $table->string('country')->nullable(); // País
                $table->string('city')->nullable(); // Ciudad
                $table->string('severity')->default('info'); // info, warning, error, critical
                $table->timestamp('created_at')->useCurrent();

                // Índices para búsquedas rápidas
                $table->index(['user_id', 'created_at']);
                $table->index(['model_type', 'model_id']);
                $table->index('action');
                $table->index('severity');
                $table->index('created_at');
            });
        } else {
            // Si la tabla ya existe, agregar las nuevas columnas
            Schema::connection('activity_log')->table('activity_logs', function (Blueprint $table) {
                if (!Schema::connection('activity_log')->hasColumn('activity_logs', 'old_values')) {
                    $table->json('old_values')->nullable()->after('properties');
                }
                if (!Schema::connection('activity_log')->hasColumn('activity_logs', 'new_values')) {
                    $table->json('new_values')->nullable()->after('old_values');
                }
                if (!Schema::connection('activity_log')->hasColumn('activity_logs', 'country')) {
                    $table->string('country')->nullable()->after('new_values');
                }
                if (!Schema::connection('activity_log')->hasColumn('activity_logs', 'city')) {
                    $table->string('city')->nullable()->after('country');
                }
                if (!Schema::connection('activity_log')->hasColumn('activity_logs', 'severity')) {
                    $table->string('severity')->default('info')->after('city');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('activity_log')->dropIfExists('activity_logs');
    }
};
