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
        Schema::create('treatment_results', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 120);
            $table->text('descripcion');
            $table->string('servicio', 80)->nullable();
            $table->string('imagen_antes');
            $table->string('imagen_despues');
            $table->boolean('visible')->default(true);
            $table->unsignedSmallInteger('orden')->default(0);
            $table->timestamps();

            $table->index(['visible', 'orden']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('treatment_results');
    }
};
