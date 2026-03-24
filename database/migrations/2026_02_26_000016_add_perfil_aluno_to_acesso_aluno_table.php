<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('acesso_aluno')) {
            return;
        }

        if (!Schema::hasColumn('acesso_aluno', 'perfil_aluno')) {
            Schema::table('acesso_aluno', function (Blueprint $table) {
                $table->string('perfil_aluno', 60)->nullable()->after('password');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('acesso_aluno')) {
            return;
        }

        if (Schema::hasColumn('acesso_aluno', 'perfil_aluno')) {
            Schema::table('acesso_aluno', function (Blueprint $table) {
                $table->dropColumn('perfil_aluno');
            });
        }
    }
};
