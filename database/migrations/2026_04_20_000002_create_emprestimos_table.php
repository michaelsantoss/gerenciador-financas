<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('emprestimos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->decimal('valor', 15, 2);
            $table->decimal('taxa_juros', 5, 2)->default(30.00);
            $table->decimal('valor_total', 15, 2);
            $table->enum('frequencia_pagamento', ['semanal', 'mensal'])->default('mensal');
            $table->date('data_vencimento');
            $table->enum('status', ['ativo', 'pago', 'atrasado'])->default('ativo');
            $table->timestamps();
        });
    }

    public function down() { Schema::dropIfExists('emprestimos'); }
};
