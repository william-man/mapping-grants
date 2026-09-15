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
        Schema::create('recipient_individuals', function (Blueprint $table) {
            $table->id();
            $table->string('individualID')->unique();
            $table->string('name');
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->string('regionName')->nullable();
            $table->string('regionCode')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recipient_individual');
    }
};
