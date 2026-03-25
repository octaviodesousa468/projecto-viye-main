<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('acesso')) {
            return;
        }

        DB::statement('ALTER TABLE acesso MODIFY email VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE acesso MODIFY palavrapasse VARCHAR(255) NOT NULL');
        DB::statement('ALTER TABLE acesso MODIFY aluno_id INT(11) NULL');
    }

    public function down(): void
    {
        if (!Schema::hasTable('acesso')) {
            return;
        }

        DB::statement('ALTER TABLE acesso MODIFY email VARCHAR(50) NOT NULL');
        DB::statement('ALTER TABLE acesso MODIFY palavrapasse VARCHAR(50) NOT NULL');
        DB::statement('ALTER TABLE acesso MODIFY aluno_id INT(11) NOT NULL');
    }
};

