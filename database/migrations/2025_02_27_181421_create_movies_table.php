<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');  // Título de la película
            $table->text('overview')->nullable();  // Descripción
            $table->string('poster_path')->nullable();  // URL de la imagen
            $table->integer('tmdb_id')->unique();  // ID de la película en TMDb
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
