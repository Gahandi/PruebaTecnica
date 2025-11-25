<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero agregar la columna spaces_id
        Schema::table('coupons', function (Blueprint $table) {
            $table->foreignId('spaces_id')->nullable()->after('id')->constrained('spaces')->onDelete('cascade');
        });
        
        // Remover el índice único existente en code si existe
        try {
            Schema::table('coupons', function (Blueprint $table) {
                $table->dropUnique(['code']);
            });
        } catch (\Exception $e) {
            // El índice puede no existir o tener otro nombre, continuar
        }
        
        // Crear índice único compuesto para code + spaces_id
        // Nota: MySQL permite múltiples NULLs en un índice único, así que esto funcionará para cupones globales
        Schema::table('coupons', function (Blueprint $table) {
            $table->unique(['code', 'spaces_id'], 'coupons_code_spaces_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropUnique(['code', 'spaces_id']);
            $table->dropForeign(['spaces_id']);
            $table->dropColumn('spaces_id');
            // Restaurar el unique en code
            $table->unique('code');
        });
    }
};
