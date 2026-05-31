<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutorizacionToCitas extends Migration
{
    public function up()
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->string('numero_autorizacion', 60)->nullable()->after('comentario');
            $table->decimal('copago_cobrado', 10, 2)->nullable()->after('numero_autorizacion');
        });
    }

    public function down()
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropColumn(['numero_autorizacion', 'copago_cobrado']);
        });
    }
}
