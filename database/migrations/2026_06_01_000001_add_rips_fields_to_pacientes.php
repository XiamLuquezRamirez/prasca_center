<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->string('incapacidad', 2)->default('NO')->after('municipio');
            $table->string('cod_pais_origen', 3)->default('170')->after('incapacidad');
        });
    }

    public function down(): void
    {
        Schema::table('pacientes', function (Blueprint $table) {
            $table->dropColumn(['incapacidad', 'cod_pais_origen']);
        });
    }
};
