<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Remove o único padrão do Laravel
            $table->dropUnique(['email']);
            // Cria o único por empresa
            $table->unique(['email', 'empresa_id']);
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email', 'empresa_id']);
            $table->unique('email');
        });
    }
};
