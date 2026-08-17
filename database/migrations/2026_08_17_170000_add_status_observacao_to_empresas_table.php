<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->string('status', 20)->default('ativo')->after('cnpj');
            $table->text('observacao')->nullable()->after('status');
        });

        DB::table('empresas')->where('ativo', true)->update(['status' => 'ativo']);
        DB::table('empresas')->where('ativo', false)->update(['status' => 'inativo']);

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('ativo');
        });
    }

    public function down()
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->boolean('ativo')->default(true)->after('cnpj');
        });

        DB::table('empresas')->where('status', 'ativo')->update(['ativo' => true]);
        DB::table('empresas')->where('status', '!=', 'ativo')->update(['ativo' => false]);

        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn(['status', 'observacao']);
        });
    }
};
