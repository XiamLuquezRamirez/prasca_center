<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Campos RIPS en consultas de psicología (puede ya estar parcialmente aplicado)
        Schema::table('consultas_psicologica', function (Blueprint $table) {
            if (!Schema::hasColumn('consultas_psicologica', 'modalidad_grupo_servicio'))
                $table->string('modalidad_grupo_servicio', 2)->nullable()->after('otra_impresion_diagnostica');
            if (!Schema::hasColumn('consultas_psicologica', 'cod_servicio'))
                $table->unsignedInteger('cod_servicio')->nullable()->after('modalidad_grupo_servicio');
            if (!Schema::hasColumn('consultas_psicologica', 'finalidad_tecnologia_salud'))
                $table->string('finalidad_tecnologia_salud', 2)->nullable()->after('cod_servicio');
            if (!Schema::hasColumn('consultas_psicologica', 'causa_motivo_atencion'))
                $table->string('causa_motivo_atencion', 2)->nullable()->after('finalidad_tecnologia_salud');
            if (!Schema::hasColumn('consultas_psicologica', 'tipo_diagnostico_principal'))
                $table->string('tipo_diagnostico_principal', 2)->nullable()->after('causa_motivo_atencion');
            if (!Schema::hasColumn('consultas_psicologica', 'dx_relacionado2'))
                $table->text('dx_relacionado2')->nullable()->after('tipo_diagnostico_principal');
            if (!Schema::hasColumn('consultas_psicologica', 'dx_relacionado3'))
                $table->text('dx_relacionado3')->nullable()->after('dx_relacionado2');
            if (!Schema::hasColumn('consultas_psicologica', 'concepto_recaudo'))
                $table->string('concepto_recaudo', 2)->nullable()->after('dx_relacionado3');
        });

        // Mismos campos en consultas de neuropsicología (tabla más simple, after 'estado')
        Schema::table('consultas_psicologica_neuro', function (Blueprint $table) {
            if (!Schema::hasColumn('consultas_psicologica_neuro', 'modalidad_grupo_servicio'))
                $table->string('modalidad_grupo_servicio', 2)->nullable()->after('estado');
            if (!Schema::hasColumn('consultas_psicologica_neuro', 'cod_servicio'))
                $table->unsignedInteger('cod_servicio')->nullable()->after('modalidad_grupo_servicio');
            if (!Schema::hasColumn('consultas_psicologica_neuro', 'finalidad_tecnologia_salud'))
                $table->string('finalidad_tecnologia_salud', 2)->nullable()->after('cod_servicio');
            if (!Schema::hasColumn('consultas_psicologica_neuro', 'causa_motivo_atencion'))
                $table->string('causa_motivo_atencion', 2)->nullable()->after('finalidad_tecnologia_salud');
            if (!Schema::hasColumn('consultas_psicologica_neuro', 'tipo_diagnostico_principal'))
                $table->string('tipo_diagnostico_principal', 2)->nullable()->after('causa_motivo_atencion');
            if (!Schema::hasColumn('consultas_psicologica_neuro', 'impresion_diagnostica'))
                $table->text('impresion_diagnostica')->nullable()->after('tipo_diagnostico_principal');
            if (!Schema::hasColumn('consultas_psicologica_neuro', 'dx_relacionado2'))
                $table->text('dx_relacionado2')->nullable()->after('impresion_diagnostica');
            if (!Schema::hasColumn('consultas_psicologica_neuro', 'dx_relacionado3'))
                $table->text('dx_relacionado3')->nullable()->after('dx_relacionado2');
            if (!Schema::hasColumn('consultas_psicologica_neuro', 'concepto_recaudo'))
                $table->string('concepto_recaudo', 2)->nullable()->after('dx_relacionado3');
        });

        // Tipo de identificación del profesional (requerido en RIPS)
        Schema::table('profesionales', function (Blueprint $table) {
            if (!Schema::hasColumn('profesionales', 'tipo_identificacion'))
                $table->string('tipo_identificacion', 2)->nullable()->after('identificacion');
        });
    }

    public function down(): void
    {
        Schema::table('consultas_psicologica', function (Blueprint $table) {
            $table->dropColumn([
                'modalidad_grupo_servicio', 'cod_servicio', 'finalidad_tecnologia_salud',
                'causa_motivo_atencion', 'tipo_diagnostico_principal',
                'dx_relacionado2', 'dx_relacionado3', 'concepto_recaudo',
            ]);
        });

        Schema::table('consultas_psicologica_neuro', function (Blueprint $table) {
            $table->dropColumn([
                'modalidad_grupo_servicio', 'cod_servicio', 'finalidad_tecnologia_salud',
                'causa_motivo_atencion', 'tipo_diagnostico_principal',
                'impresion_diagnostica', 'dx_relacionado2', 'dx_relacionado3', 'concepto_recaudo',
            ]);
        });

        Schema::table('profesionales', function (Blueprint $table) {
            $table->dropColumn('tipo_identificacion');
        });
    }
};
