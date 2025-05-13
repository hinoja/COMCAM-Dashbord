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
        Schema::create('forme_essences', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('forme_id')->constrained();
            $table->foreignId('type_id')->constrained();
            $table->foreignId('essence_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('forme_essences');
    }
};
