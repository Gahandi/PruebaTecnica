<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->integer('max_uses')->nullable()->after('expires_at')->comment('Límite total de usos');
            $table->integer('uses_count')->default(0)->after('max_uses')->comment('Usos actuales');
            $table->integer('max_uses_per_user')->nullable()->after('uses_count')->comment('Límite por usuario');
            $table->decimal('min_order_amount', 10, 2)->nullable()->after('max_uses_per_user')->comment('Monto mínimo de compra');
            $table->boolean('is_active')->default(true)->after('min_order_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn(['max_uses', 'uses_count', 'max_uses_per_user', 'min_order_amount', 'is_active']);
        });
    }
};
