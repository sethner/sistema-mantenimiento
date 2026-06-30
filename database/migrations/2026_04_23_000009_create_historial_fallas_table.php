<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_fallas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained()->onDelete('cascade');
            $table->foreignId('mantenimiento_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('componente_id')->nullable()->constrained()->onDelete('set null');
            $table->text('descripcion');
            $table->string('tipo')->default('correctivo'); // correctivo, preventivo
            $table->string('resolucion')->nullable();
            $table->date('fecha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_fallas');
    }
};
