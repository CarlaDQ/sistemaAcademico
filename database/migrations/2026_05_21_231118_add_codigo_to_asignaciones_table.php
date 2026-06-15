<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCodigoToAsignacionesTable extends Migration
{
    public function up()
    {
        Schema::table('asignaciones', function (Blueprint $table) {
            $table->string('codigo', 20)->nullable()->after('id_anio');
        });
    }

    public function down()
    {
        Schema::table('asignaciones', function (Blueprint $table) {
            $table->dropColumn('codigo');
        });
    }
}
