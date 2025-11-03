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
        Schema::table('ticket_reservations', function (Blueprint $table) {
            // Solo agregar campos que no existen
            if (!Schema::hasColumn('ticket_reservations', 'reserved_until')) {
                $table->timestamp('reserved_until')->after('id');
            }
            if (!Schema::hasColumn('ticket_reservations', 'is_active')) {
                $table->boolean('is_active')->after('session_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ticket_reservations', function (Blueprint $table) {
            $table->dropColumn([
                'reserved_until',
            ]);
        });
    }
};
