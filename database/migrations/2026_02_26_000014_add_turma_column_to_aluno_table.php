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

        if (!Schema::hasColumn('aluno', 'turma')) {
            Schema::table('aluno', function (Blueprint $table) {
                $table->string('turma', 80)->nullable()->after('encarregados');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('aluno')) {
            return;
        }

        if (Schema::hasColumn('aluno', 'turma')) {
            Schema::table('aluno', function (Blueprint $table) {
                $table->dropColumn('turma');
            });
        }
    }
};
