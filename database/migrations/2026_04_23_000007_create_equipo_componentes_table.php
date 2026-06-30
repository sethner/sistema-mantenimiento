<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipo_componentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('equipo_id')->constrained()->onDelete('cascade');
            $table->foreignId('componente_id')->constrained()->onDelete('cascade');
            $table->string('estado')->default('bueno');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipo_componentes');
    }
};
