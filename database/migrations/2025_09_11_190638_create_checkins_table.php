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
        Schema::create('checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->dateTime('scanned_at');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkins', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Removes the 'deleted_at' column
        });
    }
};
