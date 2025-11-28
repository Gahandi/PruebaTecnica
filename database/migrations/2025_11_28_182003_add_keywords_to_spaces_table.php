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
        Schema::table('spaces', function (Blueprint $table) {
            if (!Schema::hasColumn('spaces', 'keywords')) {
                // Intentar agregar después de contact_phone si existe, sino al final
                if (Schema::hasColumn('spaces', 'contact_phone')) {
                    $table->text('keywords')->nullable()->after('contact_phone');
                } else {
                    $table->text('keywords')->nullable();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spaces', function (Blueprint $table) {
            $table->dropColumn('keywords');
        });
    }
};
