<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('aluno')) {
            return;
        }

        Schema::table('aluno', function (Blueprint $table) {
            if (!Schema::hasColumn('aluno', 'desempenho')) {
                $table->decimal('desempenho', 5, 2)->nullable()->after('turma');
            }

            if (!Schema::hasColumn('aluno', 'descricao')) {
                $table->string('descricao', 255)->nullable()->after('desempenho');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('aluno')) {
            return;
        }

        Schema::table('aluno', function (Blueprint $table) {
            if (Schema::hasColumn('aluno', 'descricao')) {
                $table->dropColumn('descricao');
            }

            if (Schema::hasColumn('aluno', 'desempenho')) {
                $table->dropColumn('desempenho');
            }
        });
    }
};
