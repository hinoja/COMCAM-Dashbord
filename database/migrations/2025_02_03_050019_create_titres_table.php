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
        Schema::create('titres', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->string('localisation')->nullable();
            $table->float('volume');
            $table->foreignId('essence_id')->constrained();
            $table->foreignId('forme_id')->constrained();
            $table->foreignId('zone_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titres');
    }
};
