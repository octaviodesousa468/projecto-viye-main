<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private function hasColumn(string $table, string $column): bool
    {
        $cols = DB::select("PRAGMA table_info($table)");
        foreach ($cols as $col) {
            if (($col->name ?? null) === $column) {
                return true;
            }
        }
        return false;
    }

    public function up(): void
    {
        if (!$this->hasColumn('acesso_aluno', 'acesso')) {
            DB::statement("ALTER TABLE acesso_aluno ADD COLUMN acesso varchar DEFAULT 'ativo'");
        }

        if (!$this->hasColumn('acesso_professor', 'acesso')) {
            DB::statement("ALTER TABLE acesso_professor ADD COLUMN acesso varchar DEFAULT 'ativo'");
        }
    }

    public function down(): void
    {
        // SQLite nao suporta DROP COLUMN facilmente sem recriar tabela.
    }
};
