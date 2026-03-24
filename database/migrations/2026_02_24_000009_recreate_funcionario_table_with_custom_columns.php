<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('funcionario');

        Schema::create('funcionario', function (Blueprint $table) {
            $table->string('nome', 60);
            $table->date('data_nascimento')->nullable();
            $table->enum('sexo', ['Masculino', 'Femenino'])->nullable();
            $table->string('nacionalidade', 20)->default('Angola');
            $table->string('bi_passaporte', 21);
            $table->string('contacto', 30)->nullable();
            $table->string('email', 35)->nullable();
            $table->string('formacao', 90)->nullable();
            $table->string('nivel_academico', 25)->nullable();
            $table->string('endereco', 50)->nullable();
            $table->string('funcao', 50)->nullable();
            $table->string('departamento', 40)->nullable();

            $table->primary('bi_passaporte');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('funcionario');

        Schema::create('funcionario', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('email')->unique();
            $table->string('telefone');
            $table->string('funcao');
            $table->string('password');
            $table->timestamps();
        });
    }
};
