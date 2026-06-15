<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateEstudianteMateriaTable extends Migration
{
    public function up()
    {
        Schema::dropIfExists('estudiante_materia');
        Schema::create('estudiante_materia', function ($table) {
            $table->unsignedInteger('id_estudiante');
            $table->unsignedSmallInteger('id_materia');
            $table->timestamps();

            $table->primary(['id_estudiante', 'id_materia']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('estudiante_materia');
    }
}
