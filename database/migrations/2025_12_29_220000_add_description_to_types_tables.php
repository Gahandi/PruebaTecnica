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
        // Add description to type_events table if not exists
        if (!Schema::hasColumn('type_events', 'description')) {
            Schema::table('type_events', function (Blueprint $table) {
                $table->text('description')->nullable()->after('name');
            });
        }

        // Add description to ticket_types table if not exists
        if (!Schema::hasColumn('ticket_types', 'description')) {
            Schema::table('ticket_types', function (Blueprint $table) {
                $table->text('description')->nullable()->after('name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('type_events', 'description')) {
            Schema::table('type_events', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }

        if (Schema::hasColumn('ticket_types', 'description')) {
            Schema::table('ticket_types', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
