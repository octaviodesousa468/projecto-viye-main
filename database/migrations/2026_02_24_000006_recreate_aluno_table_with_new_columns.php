<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('aluno');

        Schema::create('aluno', function (Blueprint $table) {
            $table->string('nome', 60);
            $table->date('data_nascimento');
            $table->enum('sexo', ['Masculino', 'Feminino']);
            $table->string('bi', 21);
            $table->string('nacionalidade', 20);
            $table->string('encarregados', 40);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aluno');

        Schema::create('aluno', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('telefone');
            $table->string('curso');
            $table->string('password');
            $table->timestamps();
        });
    }
};
