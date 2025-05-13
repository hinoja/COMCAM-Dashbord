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
            $table->integer('exercice');
            $table->string('nom');
            $table->string('localisation');
            $table->foreignId('zone_id')->constrained();

            // $table->foreignId('essence_id')->constrained();
            // $table->foreignId('forme_id')->constrained();
            // $table->foreignId('type_id')->constrained();
            // $table->float('volume');
            // $table->float('VolumeRestant')->nullable();
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
