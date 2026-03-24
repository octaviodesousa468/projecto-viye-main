<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('acesso_aluno') && !Schema::hasColumn('acesso_aluno', 'acesso')) {
            Schema::table('acesso_aluno', function (Blueprint $table) {
                $table->string('acesso', 20)->default('ativo');
            });
        }

        if (Schema::hasTable('acesso_professor') && !Schema::hasColumn('acesso_professor', 'acesso')) {
            Schema::table('acesso_professor', function (Blueprint $table) {
                $table->string('acesso', 20)->default('ativo');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('acesso_aluno') && Schema::hasColumn('acesso_aluno', 'acesso')) {
            Schema::table('acesso_aluno', function (Blueprint $table) {
                $table->dropColumn('acesso');
            });
        }

        if (Schema::hasTable('acesso_professor') && Schema::hasColumn('acesso_professor', 'acesso')) {
            Schema::table('acesso_professor', function (Blueprint $table) {
                $table->dropColumn('acesso');
            });
        }
    }
};
