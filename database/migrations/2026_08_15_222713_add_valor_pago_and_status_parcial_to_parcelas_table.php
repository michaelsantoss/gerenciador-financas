<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('parcelas', function (Blueprint $table) {
            $table->decimal('valor_pago', 15, 2)->default(0)->after('valor');
            $table->timestamp('data_pagamento')->nullable()->after('data_vencimento');
        });

        DB::statement("ALTER TABLE parcelas MODIFY status ENUM('pendente', 'parcial', 'pago', 'atrasado') NOT NULL DEFAULT 'pendente'");
    }

    public function down()
    {
        DB::statement("UPDATE parcelas SET status = 'pendente' WHERE status = 'parcial'");
        DB::statement("ALTER TABLE parcelas MODIFY status ENUM('pendente', 'pago', 'atrasado') NOT NULL DEFAULT 'pendente'");

        Schema::table('parcelas', function (Blueprint $table) {
            $table->dropColumn(['valor_pago', 'data_pagamento']);
        });
    }
};
