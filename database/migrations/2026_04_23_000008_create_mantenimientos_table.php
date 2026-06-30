<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mantenimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // técnico
            $table->string('tipo')->default('correctivo');
            $table->text('descripcion');
            $table->text('diagnostico')->nullable();
            $table->text('accion')->nullable();
            $table->date('proxima_fecha')->nullable();
            $table->date('fecha');
            $table->string('estado')->default('pendiente');
            $table->decimal('costo', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mantenimientos');
    }
};
