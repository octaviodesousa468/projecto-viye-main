<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if (!Schema::hasTable('turma') && Schema::hasTable('turmas')) {
            Schema::rename('turmas', 'turma');
        }

        if (!Schema::hasTable('turma')) {
            Schema::create('turma', function (Blueprint $table) {
                $table->id();
                $table->string('nome_turma', 35);
                $table->string('idade_alunos', 20)->nullable();
                $table->string('professor', 60);
                $table->string('professor_auxiliar', 60)->nullable();
                $table->time('tempo_aula')->nullable();
                $table->integer('professor_id')->nullable();
                $table->integer('turma_id')->nullable();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('turma')) {
            Schema::table('turma', function (Blueprint $table) {
                if (!Schema::hasColumn('turma', 'nome_turma')) {
                    $table->string('nome_turma', 35)->nullable();
                }
                if (!Schema::hasColumn('turma', 'idade_alunos')) {
                    $table->string('idade_alunos', 20)->nullable();
                }
                if (!Schema::hasColumn('turma', 'professor')) {
                    $table->string('professor', 60)->nullable();
                }
                if (!Schema::hasColumn('turma', 'professor_auxiliar')) {
                    $table->string('professor_auxiliar', 60)->nullable();
                }
                if (!Schema::hasColumn('turma', 'tempo_aula')) {
                    $table->time('tempo_aula')->nullable();
                }
                if (!Schema::hasColumn('turma', 'professor_id')) {
                    $table->integer('professor_id')->nullable();
                }
                if (!Schema::hasColumn('turma', 'turma_id')) {
                    $table->integer('turma_id')->nullable();
                }
                if (!Schema::hasColumn('turma', 'created_at')) {
                    $table->timestamp('created_at')->nullable();
                }
                if (!Schema::hasColumn('turma', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable();
                }
            });
        }

        if (Schema::hasTable('turmas') && Schema::hasTable('turma')) {
            $registos = DB::table('turmas')->get();
            foreach ($registos as $registo) {
                $nomeTurma = trim((string) data_get($registo, 'nome_turma'));
                if ($nomeTurma === '') {
                    continue;
                }

                $existe = DB::table('turma')
                    ->whereRaw('LOWER(TRIM(nome_turma)) = ?', [mb_strtolower($nomeTurma)])
                    ->exists();

                if ($existe) {
                    continue;
                }

                DB::table('turma')->insert([
                    'nome_turma' => data_get($registo, 'nome_turma'),
                    'idade_alunos' => data_get($registo, 'idade_alunos'),
                    'professor' => data_get($registo, 'professor'),
                    'professor_auxiliar' => data_get($registo, 'professor_auxiliar'),
                    'tempo_aula' => data_get($registo, 'tempo_aula'),
                    'professor_id' => data_get($registo, 'professor_id') ?? data_get($registo, 'id_professor'),
                    'turma_id' => data_get($registo, 'turma_id') ?? data_get($registo, 'id'),
                    'created_at' => data_get($registo, 'created_at') ?: now(),
                    'updated_at' => data_get($registo, 'updated_at') ?: now(),
                ]);
            }
        }

        if (Schema::hasTable('turma')) {
            if (Schema::hasColumn('turma', 'id_professor') && Schema::hasColumn('turma', 'professor_id')) {
                DB::statement('UPDATE turma SET professor_id = id_professor WHERE (professor_id IS NULL OR professor_id = 0) AND id_professor IS NOT NULL');
            }

            if (Schema::hasColumn('turma', 'turma_id') && Schema::hasColumn('turma', 'id')) {
                DB::statement('UPDATE turma SET turma_id = id WHERE turma_id IS NULL OR turma_id = 0');
            }

            if (Schema::hasColumn('turma', 'tempo_aula_diaria') && Schema::hasColumn('turma', 'tempo_aula')) {
                if ($driver === 'mysql') {
                    DB::statement("UPDATE turma SET tempo_aula = STR_TO_DATE(CONCAT(tempo_aula_diaria, ':00'), '%H:%i:%s') WHERE (tempo_aula IS NULL) AND tempo_aula_diaria REGEXP '^[0-2][0-9]:[0-5][0-9]$'");
                } else {
                    DB::statement("UPDATE turma SET tempo_aula = tempo_aula_diaria || ':00' WHERE tempo_aula IS NULL AND tempo_aula_diaria IS NOT NULL AND LENGTH(tempo_aula_diaria) = 5");
                }
            }

            if (Schema::hasColumn('turma', 'tempo_aula_diario') && Schema::hasColumn('turma', 'tempo_aula')) {
                if ($driver === 'mysql') {
                    DB::statement("UPDATE turma SET tempo_aula = STR_TO_DATE(CONCAT(tempo_aula_diario, ':00'), '%H:%i:%s') WHERE (tempo_aula IS NULL) AND tempo_aula_diario REGEXP '^[0-2][0-9]:[0-5][0-9]$'");
                } else {
                    DB::statement("UPDATE turma SET tempo_aula = tempo_aula_diario || ':00' WHERE tempo_aula IS NULL AND tempo_aula_diario IS NOT NULL AND LENGTH(tempo_aula_diario) = 5");
                }
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('turmas') && Schema::hasTable('turma')) {
            Schema::rename('turma', 'turmas');
        }
    }
};
