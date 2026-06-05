<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultas_psicologica', function (Blueprint $table) {
            $table->unsignedInteger('dx_relacionado1')->nullable()->after('impresion_diagnostica');
            $table->string('grupo_servicios', 2)->nullable()->after('modalidad_grupo_servicio');
            $table->time('hora_fin')->nullable()->after('fecha_consulta');
        });

        Schema::table('consultas_psicologica_neuro', function (Blueprint $table) {
            $table->unsignedInteger('dx_relacionado1')->nullable()->after('impresion_diagnostica');
            $table->string('grupo_servicios', 2)->nullable()->after('modalidad_grupo_servicio');
            $table->time('hora_fin')->nullable()->after('fecha_consulta');
        });
    }

    public function down(): void
    {
        Schema::table('consultas_psicologica', function (Blueprint $table) {
            $table->dropColumn(['dx_relacionado1', 'grupo_servicios', 'hora_fin']);
        });

        Schema::table('consultas_psicologica_neuro', function (Blueprint $table) {
            $table->dropColumn(['dx_relacionado1', 'grupo_servicios', 'hora_fin']);
        });
    }
};
