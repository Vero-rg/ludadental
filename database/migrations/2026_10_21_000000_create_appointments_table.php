<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('codigo_folio', 20)->unique();
            $table->string('paciente_nombre', 120);
            $table->string('paciente_telefono', 20);
            $table->string('paciente_email', 150)->nullable();
            $table->string('servicio', 80);
            $table->date('fecha');
            $table->time('hora');
            $table->enum('tipo_paciente', ['adulto', 'nino'])->default('adulto');
            $table->text('notas')->nullable();
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'completada'])
                ->default('pendiente');
            $table->timestamps();

            $table->index(['fecha', 'hora', 'estado']);
            $table->index('estado');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
