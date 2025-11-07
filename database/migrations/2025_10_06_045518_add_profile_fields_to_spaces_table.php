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
            // Solo agregar campos que no existen
            if (!Schema::hasColumn('spaces', 'color_primary')) {
                $table->string('color_primary')->default('#3B82F6')->after('logo');
            }
            if (!Schema::hasColumn('spaces', 'color_secondary')) {
                $table->string('color_secondary')->default('#1E40AF')->after('color_primary');
            }
            if (!Schema::hasColumn('spaces', 'about')) {
                $table->text('about')->nullable()->after('color_secondary');
            }
            if (!Schema::hasColumn('spaces', 'location')) {
                $table->string('location')->nullable()->after('about');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spaces', function (Blueprint $table) {
            $table->dropColumn([
                'color_primary',
                'color_secondary',
                'about',
                'location'
            ]);
        });
    }
};
