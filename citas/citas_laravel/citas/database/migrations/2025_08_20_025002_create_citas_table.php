<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_paciente');
            $table->unsignedBigInteger('id_medico');
            $table->date('fecha');
            $table->time('hora');
            $table->enum('estado', ['PENDIENTE', 'CONFIRMADA', 'CANCELADA', 'ATENDIDA'])->default('PENDIENTE');
            $table->timestamps();

            $table->foreign('id_paciente')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('id_medico')->references('id')->on('usuarios')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
