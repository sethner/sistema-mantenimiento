<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('componentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_id')->constrained('tipo_equipos')->onDelete('cascade');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_componentes')->nullOnDelete();
            $table->string('nombre');
            $table->string('tipo')->nullable();
            $table->string('imagen')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('componentes');
    }
};
