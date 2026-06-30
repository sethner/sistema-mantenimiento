<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimiento_fotos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mantenimiento_id')->constrained()->onDelete('cascade');
            $table->string('ruta');
            $table->string('nombre_original');
            $table->string('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimiento_fotos');
    }
};
