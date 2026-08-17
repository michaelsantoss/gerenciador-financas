<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->string('apelido')->nullable()->after('nome');
            $table->string('cpf', 14)->nullable()->after('telefone');
            $table->string('rg', 20)->nullable()->after('cpf');
            $table->string('email')->nullable()->after('rg');
            $table->string('quem_indicou')->nullable()->after('email');
            $table->text('observacao')->nullable()->after('quem_indicou');
        });
    }

    public function down()
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn(['apelido', 'cpf', 'rg', 'email', 'quem_indicou', 'observacao']);
        });
    }
};
