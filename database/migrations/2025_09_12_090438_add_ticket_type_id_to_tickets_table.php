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
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('ticket_type_id')->after('order_id')->nullable()->constrained('ticket_types')->onDelete('cascade');
        });
        
        // Actualizar tickets existentes con un ticket_type_id por defecto
        \App\Models\Ticket::whereNull('ticket_type_id')->update(['ticket_type_id' => 1]);
        
        // Hacer la columna no nullable después de actualizar
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('ticket_type_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['ticket_type_id']);
            $table->dropColumn('ticket_type_id');
        });
    }
};
