<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medico_especialidad', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_medico');
            $table->unsignedBigInteger('id_especialidad');
            $table->timestamps();

            $table->foreign('id_medico')->references('id')->on('usuarios')->onDelete('cascade');
            $table->foreign('id_especialidad')->references('id')->on('especialidades')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medico_especialidad');
    }
};
