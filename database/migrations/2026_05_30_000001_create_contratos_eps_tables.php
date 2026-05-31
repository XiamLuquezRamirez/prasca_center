<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContratosEpsTables extends Migration
{
    public function up()
    {
        Schema::create('contratos_eps', function (Blueprint $table) {
            $table->id();
            $table->integer('id_eps');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->enum('estado', ['activo', 'borrador'])->default('borrador');
            $table->timestamps();
            $table->foreign('id_eps')->references('id')->on('eps')->onDelete('cascade');
        });

        Schema::create('planes_eps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_contrato');
            $table->string('nombre', 120);
            $table->string('descripcion', 255)->nullable();
            $table->unsignedInteger('limite_consultas')->nullable(); // NULL = ilimitado
            $table->enum('periodo', ['anual', 'sin_periodo'])->default('anual');
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            $table->foreign('id_contrato')->references('id')->on('contratos_eps')->onDelete('cascade');
        });

        Schema::create('copagos_eps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_plan');
            $table->string('tipo_servicio', 120);
            $table->decimal('monto_copago', 10, 2);
            $table->unsignedInteger('max_sesiones')->nullable(); // NULL = ilimitado
            $table->timestamps();
            $table->foreign('id_plan')->references('id')->on('planes_eps')->onDelete('cascade');
        });

        Schema::create('paciente_planes_eps', function (Blueprint $table) {
            $table->id();
            $table->integer('id_paciente');
            $table->unsignedBigInteger('id_plan');
            $table->string('numero_poliza', 60)->nullable();
            $table->date('fecha_vinculacion')->nullable();
            $table->enum('estado', ['activo', 'inactivo'])->default('activo');
            $table->timestamps();
            $table->foreign('id_paciente')->references('id')->on('pacientes')->onDelete('cascade');
            $table->foreign('id_plan')->references('id')->on('planes_eps')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('paciente_planes_eps');
        Schema::dropIfExists('copagos_eps');
        Schema::dropIfExists('planes_eps');
        Schema::dropIfExists('contratos_eps');
    }
}
