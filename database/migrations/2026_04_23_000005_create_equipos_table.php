<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->string('nombre');
            $table->string('marca')->nullable();
            $table->string('modelo')->nullable();
            $table->string('numero_serie')->nullable();
            $table->string('numero_inventario')->nullable();
            $table->string('procesador')->nullable();
            $table->string('ram')->nullable();
            $table->string('almacenamiento')->nullable();
            $table->string('sistema_operativo')->nullable();
            $table->string('proveedor')->nullable();
            $table->decimal('valor_adquisicion', 10, 2)->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->date('fecha_garantia')->nullable();
            $table->text('observaciones')->nullable();
            $table->integer('frecuencia_mantenimiento')->default(6)->comment('En meses');
            $table->date('proximo_mantenimiento')->nullable();
            $table->foreignId('tipo_id')->constrained('tipo_equipos')->onDelete('cascade');
            $table->string('estado')->default('operativo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
