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
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->dateTime('date');
            $table->string('address');
            $table->string('coordinates');
            $table->string('slug');
            $table->boolean('active')->default(1);
            $table->string('description');
            $table->foreignId('type_events_id')->constrained('type_events')->onDelete('cascade');
            $table->foreignId('spaces_id')->constrained('spaces')->onDelete('cascade');
            $table->foreignId('state_id')->constrained('states')->onDelete('cascade');
            $table->string('image');
            $table->string('banner');
            $table->string('banner_app');
            $table->string('icon');
            $table->string('agenda');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Removes the 'deleted_at' column
        });
    }
};
