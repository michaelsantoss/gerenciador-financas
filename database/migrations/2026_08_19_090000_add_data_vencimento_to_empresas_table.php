<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->date('data_vencimento')->default(now()->addDays(30)->toDateString())->after('plano');
        });
    }

    public function down()
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn('data_vencimento');
        });
    }
};
