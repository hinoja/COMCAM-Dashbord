Schema::create('essence_titre', function (Blueprint $table) {
    // ... existing code ...
    $table->foreign('titre_id')
          ->references('id')
          ->on('titres')
          ->onDelete('cascade');
    // ... existing code ...
});
