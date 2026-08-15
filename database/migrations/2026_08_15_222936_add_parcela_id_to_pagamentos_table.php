<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->foreignId('parcela_id')->nullable()->after('emprestimo_id')
                ->constrained('parcelas')->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('parcela_id');
        });
    }
};
