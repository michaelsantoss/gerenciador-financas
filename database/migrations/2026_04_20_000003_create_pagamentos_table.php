<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emprestimo_id')->constrained('emprestimos')->onDelete('cascade');
            $table->decimal('valor_pago', 15, 2);
            $table->date('data_pagamento');
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });
    }

    public function down() { Schema::dropIfExists('pagamentos'); }
};
