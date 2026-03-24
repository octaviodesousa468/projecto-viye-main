<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('acesso_admin')) {
            return;
        }

        Schema::create('acesso_admin', function (Blueprint $table) {
            $table->id();
            $table->string('email_encarregado')->unique();
            $table->string('password');
            $table->string('acesso', 20)->default('ativo');
            $table->string('perfil_admin', 40)->default('administrador');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acesso_admin');
    }
};

