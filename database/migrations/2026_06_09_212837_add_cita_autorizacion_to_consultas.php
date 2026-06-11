<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consultas_psicologica', function (Blueprint $table) {
            $table->unsignedInteger('cita_id')->nullable()->after('id_historia');
            $table->unsignedBigInteger('autorizacion_id')->nullable()->after('cita_id');
        });

        Schema::table('consultas_psicologica_neuro', function (Blueprint $table) {
            $table->unsignedInteger('cita_id')->nullable()->after('id_historia');
            $table->unsignedBigInteger('autorizacion_id')->nullable()->after('cita_id');
        });
    }

    public function down(): void
    {
        Schema::table('consultas_psicologica', function (Blueprint $table) {
            $table->dropColumn(['cita_id', 'autorizacion_id']);
        });

        Schema::table('consultas_psicologica_neuro', function (Blueprint $table) {
            $table->dropColumn(['cita_id', 'autorizacion_id']);
        });
    }
};
