<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('turmas', function (Blueprint $table) {
            $table->id();
            $table->string('nome_turma', 80);
            $table->string('idade_alunos', 30)->nullable();
            $table->string('professor', 100);
            $table->string('professor_auxiliar', 100)->nullable();
            $table->string('tempo_aula_diaria', 40)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('turmas');
    }
};
