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
        Schema::create('essence_titre', function (Blueprint $table) {
            $table->id();
            $table->foreignId('titre_id')->constrained();
            $table->foreignId('essence_id')->constrained();
            $table->float('volume');
            $table->float('VolumeRestant')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('essence_titre');
    }
};
