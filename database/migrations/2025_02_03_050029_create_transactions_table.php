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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->integer('exercice');
            $table->integer('numero');
            $table->foreignId('societe_id')->constrained();
            $table->string('destination');
            $table->string('pays');
            $table->foreignId('titre_id')->constrained();
            $table->foreignId('essence_id')->constrained();
            // $table->foreignId('forme_id')->constrained();
            $table->foreignId('conditionnemment_id')->constrained();
            // $table->foreignId('type_id')->constrained()->nullable();
            $table->float('volume');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
