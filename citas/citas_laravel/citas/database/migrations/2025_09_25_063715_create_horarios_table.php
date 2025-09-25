<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::create('horarios', function (Blueprint $table) {
        $table->id();
        $table->foreignId('id_usuario')->constrained('usuarios')->onDelete('cascade');
        $table->date('fecha');
        $table->time('hora_inicio');
        $table->time('hora_fin');
        $table->boolean('disponible')->default(true);
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
      Schema::dropIfExists('horarios');
    }
};
