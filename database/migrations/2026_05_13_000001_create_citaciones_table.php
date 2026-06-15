<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citaciones', function (Blueprint $table) {
            $table->id('id_citacion');
            $table->unsignedInteger('id_docente');
            $table->unsignedInteger('id_estudiante');
            $table->string('titulo', 200);
            $table->text('mensaje');
            $table->enum('tipo', ['citacion', 'aviso', 'comunicado'])->default('aviso');
            $table->date('fecha_citacion');
            $table->time('hora_citacion')->nullable();
            $table->string('lugar', 200)->nullable();
            $table->enum('estado', ['pendiente', 'enviada', 'leida', 'respondida'])->default('pendiente');
            $table->timestamps();

            $table->foreign('id_docente')->references('id_docente')->on('docentes')->onDelete('cascade');
            $table->foreign('id_estudiante')->references('id_estudiante')->on('estudiantes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citaciones');
    }
};
