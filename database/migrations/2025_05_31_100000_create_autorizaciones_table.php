<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutorizacionesTable extends Migration
{
    public function up()
    {
        Schema::connection('mysql')->create('autorizaciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_paciente');
            $table->unsignedBigInteger('id_plan');
            $table->string('numero_autorizacion', 100);
            $table->string('tipo_servicio', 150);
            $table->date('fecha_solicitud');
            $table->date('fecha_vencimiento')->nullable();
            $table->integer('sesiones_autorizadas')->nullable();
            $table->decimal('valor_copago', 10, 2)->default(0);
            $table->decimal('valor_autorizado', 10, 2)->default(0);
            $table->enum('estado', ['activa', 'agotada', 'vencida', 'anulada'])->default('activa');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });

        Schema::connection('mysql')->table('citas', function (Blueprint $table) {
            $table->unsignedBigInteger('id_autorizacion')->nullable()->after('copago_cobrado');
        });
    }

    public function down()
    {
        Schema::connection('mysql')->table('citas', function (Blueprint $table) {
            $table->dropColumn('id_autorizacion');
        });
        Schema::connection('mysql')->dropIfExists('autorizaciones');
    }
}
