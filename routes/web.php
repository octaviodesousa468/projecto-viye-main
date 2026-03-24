<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

$normalizarTextoAcesso = function (?string $valor): string {
    return mb_strtolower(trim((string) $valor));
};

$registroComAcessoAtivo = function ($query, string $tabela) use ($normalizarTextoAcesso) {
    if (!Schema::hasColumn($tabela, 'acesso')) {
        return $query->first();
    }

    $registros = $query->get();

    return $registros->first(function ($registro) use ($normalizarTextoAcesso) {
        $acesso = $normalizarTextoAcesso(data_get($registro, 'acesso'));

        return in_array($acesso, ['ativo', 'activo', '1', 'sim', 'yes'], true);
    });
};

$buscarAcessoPorIdentificador = function (string $tabela, string $identificador) use ($normalizarTextoAcesso, $registroComAcessoAtivo) {
    if (!Schema::hasTable($tabela)) {
        return null;
    }

    $colunasSenha = collect(['password', 'palavrapasse', 'senha', 'passwork'])
        ->filter(fn (string $coluna) => Schema::hasColumn($tabela, $coluna))
        ->values();

    if ($colunasSenha->isEmpty()) {
        return null;
    }

    $identificadorNormalizado = $normalizarTextoAcesso($identificador);
    $colunasLogin = collect(['email', 'email_encarregado', 'login', 'username', 'utilizador'])
        ->filter(fn (string $coluna) => Schema::hasColumn($tabela, $coluna))
        ->values();

    if ($colunasLogin->isEmpty()) {
        return null;
    }

    foreach ($colunasLogin as $colunaLogin) {
        $query = DB::table($tabela)->whereRaw('LOWER(TRIM(' . $colunaLogin . ')) = ?', [$identificadorNormalizado]);
        $registro = $registroComAcessoAtivo($query, $tabela);

        if ($registro) {
            return $registro;
        }
    }

    return null;
};

$chatUsuarioAutorizado = function (?string $email): bool {
    if (empty($email)) {
        return false;
    }

    $existeAluno = false;
    if (Schema::hasTable('acesso_aluno')) {
        $queryAluno = DB::table('acesso_aluno')->where('email_encarregado', $email);
        if (Schema::hasColumn('acesso_aluno', 'acesso')) {
            $queryAluno->where('acesso', 'ativo');
        }
        $existeAluno = $queryAluno->exists();
    }

    if ($existeAluno) {
        return true;
    }

    $existeAdmin = false;
    if (Schema::hasTable('acesso_admin')) {
        $queryAdmin = Schema::hasColumn('acesso_admin', 'email_encarregado')
            ? DB::table('acesso_admin')->where('email_encarregado', $email)
            : DB::table('acesso_admin')->where('email', $email);

        if (Schema::hasColumn('acesso_admin', 'acesso')) {
            $queryAdmin->where('acesso', 'ativo');
        }

        $existeAdmin = $queryAdmin->exists();
    }

    if ($existeAdmin) {
        return true;
    }

    if (!Schema::hasTable('acesso_professor')) {
        return false;
    }

    $queryProfessor = DB::table('acesso_professor')->where('email_encarregado', $email);
    if (Schema::hasColumn('acesso_professor', 'acesso')) {
        $queryProfessor->where('acesso', 'ativo');
    }

    return $queryProfessor->exists();
};

$chatListaContatos = function (string $meuEmail) {
    $contatos = collect();

    if (Schema::hasTable('acesso_aluno')) {
        $queryAluno = DB::table('acesso_aluno')->select('email_encarregado as email', DB::raw("'aluno' as tipo"));
        if (Schema::hasColumn('acesso_aluno', 'acesso')) {
            $queryAluno->where('acesso', 'ativo');
        }
        $contatos = $contatos->concat($queryAluno->get());
    }

    if (Schema::hasTable('acesso_professor')) {
        $queryProfessor = DB::table('acesso_professor')->select('email_encarregado as email', DB::raw("'professor' as tipo"));
        if (Schema::hasColumn('acesso_professor', 'acesso')) {
            $queryProfessor->where('acesso', 'ativo');
        }
        $contatos = $contatos->concat($queryProfessor->get());
    }

    if (Schema::hasTable('acesso_admin')) {
        if (Schema::hasColumn('acesso_admin', 'email_encarregado')) {
            $queryAdmin = DB::table('acesso_admin')->select('email_encarregado as email', DB::raw("'admin' as tipo"));
        } else {
            $queryAdmin = DB::table('acesso_admin')->select('email as email', DB::raw("'admin' as tipo"));
        }

        if (Schema::hasColumn('acesso_admin', 'acesso')) {
            $queryAdmin->where('acesso', 'ativo');
        }

        $contatos = $contatos->concat($queryAdmin->get());
    }

    return $contatos
        ->unique('email')
        ->reject(fn ($item) => data_get($item, 'email') === $meuEmail)
        ->values();
};

$obterListaProfessores = function () {
    $listaProfessores = collect();

    if (Schema::hasTable('professor')) {
        $colunaNomeProfessor = collect(['nome', 'perfil_professor', 'name'])
            ->first(fn (string $coluna) => Schema::hasColumn('professor', $coluna));

        if ($colunaNomeProfessor) {
            $queryProfessores = DB::table('professor');

            if (Schema::hasColumn('professor', 'id')) {
                $queryProfessores->select('id');
            } else {
                $queryProfessores->selectRaw('NULL as id');
            }

            $listaProfessores = $listaProfessores->concat(
                $queryProfessores
                    ->addSelect($colunaNomeProfessor . ' as nome')
                    ->orderBy($colunaNomeProfessor)
                    ->get()
            );
        }
    }

    if ($listaProfessores->isEmpty() && Schema::hasTable('acesso_professor')) {
        $colunaNomeAcessoProfessor = collect(['nome', 'perfil_professor'])
            ->first(fn (string $coluna) => Schema::hasColumn('acesso_professor', $coluna));

        if ($colunaNomeAcessoProfessor) {
            $queryAcessoProfessores = DB::table('acesso_professor');

            if (Schema::hasColumn('acesso_professor', 'id')) {
                $queryAcessoProfessores->select('id');
            } else {
                $queryAcessoProfessores->selectRaw('NULL as id');
            }

            $listaProfessores = $listaProfessores->concat(
                $queryAcessoProfessores
                    ->addSelect($colunaNomeAcessoProfessor . ' as nome')
                    ->orderBy($colunaNomeAcessoProfessor)
                    ->get()
            );
        }
    }

    return $listaProfessores
        ->filter(fn ($professor) => trim((string) data_get($professor, 'nome')) !== '')
        ->unique(fn ($professor) => mb_strtolower(trim((string) data_get($professor, 'nome'))))
        ->sortBy(fn ($professor) => mb_strtolower((string) data_get($professor, 'nome')))
        ->values();
};

$obterTabelaTurmas = function (): ?string {
    if (Schema::hasTable('turma')) {
        return 'turma';
    }

    if (Schema::hasTable('turmas')) {
        return 'turmas';
    }

    return null;
};

$obterColunaProfessorTurma = function (?string $tabelaTurmas): ?string {
    if (!$tabelaTurmas || !Schema::hasTable($tabelaTurmas)) {
        return null;
    }

    foreach (['professor_id', 'id_professor'] as $coluna) {
        if (Schema::hasColumn($tabelaTurmas, $coluna)) {
            return $coluna;
        }
    }

    return null;
};

$obterProximoIdTabela = function (string $tabela): int {
    $driver = DB::getDriverName();

    if ($driver === 'mysql') {
        $baseDados = DB::getDatabaseName();
        $resultado = DB::selectOne(
            'SELECT AUTO_INCREMENT FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?',
            [$baseDados, $tabela]
        );

        $proximoId = (int) data_get($resultado, 'AUTO_INCREMENT', 0);
        if ($proximoId > 0) {
            return $proximoId;
        }
    }

    return ((int) DB::table($tabela)->max('id')) + 1;
};

$obterPrimeiraColunaExistente = function (string $tabela, array $colunas): ?string {
    if (!Schema::hasTable($tabela)) {
        return null;
    }

    foreach ($colunas as $coluna) {
        if (Schema::hasColumn($tabela, $coluna)) {
            return $coluna;
        }
    }

    return null;
};

$obterListaTurmas = function () use ($obterTabelaTurmas, $obterPrimeiraColunaExistente) {
    $tabelaTurmas = $obterTabelaTurmas();
    if (!$tabelaTurmas) {
        return collect();
    }

    $colunaIdTurma = $obterPrimeiraColunaExistente($tabelaTurmas, ['id', 'turma_id']);
    $colunaNomeTurma = $obterPrimeiraColunaExistente($tabelaTurmas, ['nome_turma', 'nome', 'turma']);

    if (!$colunaIdTurma || !$colunaNomeTurma) {
        return collect();
    }

    return DB::table($tabelaTurmas)
        ->selectRaw($colunaIdTurma . ' as id, ' . $colunaNomeTurma . ' as nome_turma')
        ->whereNotNull($colunaNomeTurma)
        ->whereRaw('TRIM(' . $colunaNomeTurma . ") <> ''")
        ->orderBy($colunaNomeTurma)
        ->get()
        ->unique('id')
        ->values();
};

Route::get('/', function (\Illuminate\Http\Request $request) {
    $request->session()->regenerateToken();

    return view('welcome');
});

Route::get('/welcome', function (\Illuminate\Http\Request $request) {
    $request->session()->regenerateToken();

    return view('welcome');
})->name('welcome');

Route::get('/logout', function (\Illuminate\Http\Request $request) {
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('welcome');
})->name('logout');

Route::post('/login', function (\Illuminate\Http\Request $request) use ($buscarAcessoPorIdentificador) {
    $dados = $request->validate([
        'email' => ['required', 'email', 'max:255'],
        'password' => ['required', 'string'],
    ]);

    $request->session()->regenerate();

    $email = strtolower(trim((string) $dados['email']));
    $password = (string) $dados['password'];

    $senhaConfere = function (string $senhaDigitada, ?string $senhaBanco): bool {
        if (empty($senhaBanco)) {
            return false;
        }

        return Hash::check($senhaDigitada, $senhaBanco) || hash_equals($senhaBanco, $senhaDigitada);
    };

    $obterSenhaRegistro = function ($registro): ?string {
        foreach (['password', 'palavrapasse', 'senha', 'passwork'] as $colunaSenha) {
            $valor = data_get($registro, $colunaSenha);
            if (!is_null($valor) && trim((string) $valor) !== '') {
                return (string) $valor;
            }
        }

        return null;
    };

    $acesso = $buscarAcessoPorIdentificador('acesso', $email);
    if ($acesso && $senhaConfere($password, $obterSenhaRegistro($acesso))) {
        $nivel = strtolower((string) (
            data_get($acesso, 'tipo')
            ?? data_get($acesso, 'perfil')
            ?? data_get($acesso, 'nivel')
            ?? data_get($acesso, 'access_level')
            ?? data_get($acesso, 'perfil_admin')
            ?? ''
        ));

        $loginUtilizado = (string) (
            data_get($acesso, 'email')
            ?? data_get($acesso, 'email_encarregado')
            ?? data_get($acesso, 'login')
            ?? $email
        );

        $nomeUtilizado = (string) (data_get($acesso, 'nome') ?? '');

        if (str_contains($nivel, 'admin')) {
            session(['access_level' => 'admin', 'user_email' => $email, 'user_login' => $loginUtilizado, 'user_nome' => $nomeUtilizado]);
            return redirect()->route('dashboard');
        }

        if (str_contains($nivel, 'prof')) {
            session(['access_level' => 'professor', 'user_email' => $email, 'user_login' => $loginUtilizado, 'user_nome' => $nomeUtilizado]);
            return redirect()->route('telaprofessor');
        }

        session(['access_level' => 'aluno', 'user_email' => $email, 'user_login' => $loginUtilizado, 'user_nome' => $nomeUtilizado]);
        return redirect()->route('telaaluno');
    }

    $acessoAdmin = $buscarAcessoPorIdentificador('acesso_admin', $email);
    if ($acessoAdmin && $senhaConfere($password, $obterSenhaRegistro($acessoAdmin))) {
        session([
            'access_level' => 'admin',
            'user_email' => $email,
            'user_login' => (string) (data_get($acessoAdmin, 'email_encarregado') ?? data_get($acessoAdmin, 'email') ?? $email),
            'user_nome' => (string) (data_get($acessoAdmin, 'nome') ?? ''),
        ]);

        return redirect()->route('dashboard');
    }

    $acessoProfessor = $buscarAcessoPorIdentificador('acesso_professor', $email);
    if ($acessoProfessor && $senhaConfere($password, $obterSenhaRegistro($acessoProfessor))) {
        session([
            'access_level' => 'professor',
            'user_email' => $email,
            'user_login' => (string) (data_get($acessoProfessor, 'email_encarregado') ?? data_get($acessoProfessor, 'email') ?? $email),
            'user_nome' => (string) (data_get($acessoProfessor, 'nome') ?? ''),
        ]);

        return redirect()->route('telaprofessor');
    }

    $acessoAluno = $buscarAcessoPorIdentificador('acesso_aluno', $email);
    if ($acessoAluno && $senhaConfere($password, $obterSenhaRegistro($acessoAluno))) {
        session([
            'access_level' => 'aluno',
            'user_email' => $email,
            'user_login' => (string) (data_get($acessoAluno, 'email_encarregado') ?? data_get($acessoAluno, 'email') ?? $email),
            'user_nome' => (string) (data_get($acessoAluno, 'nome') ?? ''),
        ]);

        return redirect()->route('telaaluno');
    }

    return redirect()->route('welcome')->withErrors([
        'password' => 'Email ou palavra passe invalido, ou acesso inativo.',
    ])->withInput();
})->name('login.attempt');

Route::get('/telaprofessor', function () {
    return redirect()->route('telaprofessor.turma');
})->name('telaprofessor');

Route::get('/telaprofessor/turma', function () {
    $meuEmail = strtolower(trim((string) session('user_email', '')));
    $nomeSessao = trim((string) session('user_nome', ''));
    $loginSessao = trim((string) session('user_login', ''));

    $professorPerfil = null;
    $nomeProfessor = '';
    $turma = null;
    $alunosTurma = collect();
    $colunaReferenciaAluno = null;
    $colunaDesempenho = null;
    $colunaDescricao = null;

    if (Schema::hasTable('professor')) {
        if ($meuEmail !== '' && Schema::hasColumn('professor', 'email')) {
            $professorPerfil = DB::table('professor')
                ->whereRaw('LOWER(TRIM(email)) = ?', [$meuEmail])
                ->first();
        }

        if (!$professorPerfil && $nomeSessao !== '' && Schema::hasColumn('professor', 'nome')) {
            $professorPerfil = DB::table('professor')
                ->whereRaw('LOWER(TRIM(nome)) = ?', [mb_strtolower($nomeSessao)])
                ->first();
        }

        if (!$professorPerfil && $loginSessao !== '' && Schema::hasColumn('professor', 'nome')) {
            $professorPerfil = DB::table('professor')
                ->whereRaw('LOWER(TRIM(nome)) = ?', [mb_strtolower($loginSessao)])
                ->first();
        }
    }

    $nomeProfessor = trim((string) data_get($professorPerfil, 'nome', ''));
    if ($nomeProfessor === '') {
        $nomeProfessor = $nomeSessao !== '' ? $nomeSessao : $loginSessao;
    }

    if ($nomeProfessor !== '') {
        session(['user_nome' => $nomeProfessor]);
    }

    if (Schema::hasTable('turmas')) {
        if ($professorPerfil && Schema::hasColumn('turmas', 'id_professor')) {
            $idProfessor = (int) data_get($professorPerfil, 'id', 0);
            if ($idProfessor > 0) {
                $turma = DB::table('turmas')
                    ->where('id_professor', $idProfessor)
                    ->orderBy('nome_turma')
                    ->first();
            }
        }

        if (!$turma && $nomeProfessor !== '' && Schema::hasColumn('turmas', 'professor')) {
            $turma = DB::table('turmas')
                ->whereRaw('LOWER(TRIM(professor)) = ?', [mb_strtolower($nomeProfessor)])
                ->orderBy('nome_turma')
                ->first();
        }
    }

    if ($turma && Schema::hasTable('aluno')) {
        $colunasAluno = Schema::getColumnListing('aluno');
        $colunaLigacao = null;

        if (in_array('turma', $colunasAluno, true)) {
            $colunaLigacao = 'turma';
        } elseif (in_array('turma_id', $colunasAluno, true)) {
            $colunaLigacao = 'turma_id';
        }

        foreach (['id', 'bi', 'email', 'nome'] as $coluna) {
            if (in_array($coluna, $colunasAluno, true)) {
                $colunaReferenciaAluno = $coluna;
                break;
            }
        }

        foreach (['desempenho', 'nota', 'nota_final', 'media'] as $coluna) {
            if (in_array($coluna, $colunasAluno, true)) {
                $colunaDesempenho = $coluna;
                break;
            }
        }

        foreach (['descricao', 'descricao_desempenho', 'observacao'] as $coluna) {
            if (in_array($coluna, $colunasAluno, true)) {
                $colunaDescricao = $coluna;
                break;
            }
        }

        if ($colunaLigacao === 'turma_id') {
            $alunosTurma = DB::table('aluno')
                ->where('turma_id', (int) data_get($turma, 'id'))
                ->orderBy('nome')
                ->get();
        } elseif ($colunaLigacao === 'turma') {
            $alunosTurma = DB::table('aluno')
                ->where('turma', (string) data_get($turma, 'nome_turma', ''))
                ->orderBy('nome')
                ->get();
        }
    }

    return view('telaprofessor', [
        'aba' => 'turma',
        'turma' => $turma,
        'alunosTurma' => $alunosTurma,
        'nomeProfessor' => $nomeProfessor,
        'colunaReferenciaAluno' => $colunaReferenciaAluno,
        'colunaDesempenho' => $colunaDesempenho,
        'colunaDescricao' => $colunaDescricao,
    ]);
})->name('telaprofessor.turma');

Route::get('/telaprofessor/notificacoes', function () {
    $noticias = Schema::hasTable('noticias')
        ? DB::table('noticias')
            ->whereIn('destinatario', ['professores', 'geral'])
            ->orderByDesc('data_publicacao')
            ->orderByDesc('hora_publicacao')
            ->orderByDesc('id')
            ->get()
        : collect();

    return view('telaprofessor', ['aba' => 'notificacoes', 'noticias' => $noticias]);
})->name('telaprofessor.notificacoes');

Route::get('/telaprofessor/chat', function () use ($chatUsuarioAutorizado, $chatListaContatos) {
    $meuEmail = strtolower(trim((string) session('user_email', '')));
    abort_unless($chatUsuarioAutorizado($meuEmail), 403, 'Acesso nao autorizado ao chat.');

    $contatos = $chatListaContatos($meuEmail);
    $emailsAlunosDaTurma = collect();

    if (
        Schema::hasTable('professor')
        && Schema::hasTable('turmas')
        && Schema::hasTable('aluno')
        && Schema::hasTable('acesso_aluno')
        && Schema::hasColumn('acesso_aluno', 'perfil_aluno')
        && Schema::hasColumn('acesso_aluno', 'email_encarregado')
    ) {
        $nomeSessao = trim((string) session('user_nome', ''));
        $loginSessao = trim((string) session('user_login', ''));

        $professorPerfil = null;
        if ($meuEmail !== '' && Schema::hasColumn('professor', 'email')) {
            $professorPerfil = DB::table('professor')
                ->whereRaw('LOWER(TRIM(email)) = ?', [$meuEmail])
                ->first();
        }

        if (!$professorPerfil && $nomeSessao !== '' && Schema::hasColumn('professor', 'nome')) {
            $professorPerfil = DB::table('professor')
                ->whereRaw('LOWER(TRIM(nome)) = ?', [mb_strtolower($nomeSessao)])
                ->first();
        }

        if (!$professorPerfil && $loginSessao !== '' && Schema::hasColumn('professor', 'nome')) {
            $professorPerfil = DB::table('professor')
                ->whereRaw('LOWER(TRIM(nome)) = ?', [mb_strtolower($loginSessao)])
                ->first();
        }

        $turmaProfessor = null;
        $nomeProfessor = trim((string) data_get($professorPerfil, 'nome', ''));
        if ($professorPerfil && Schema::hasColumn('turma', 'id_professor')) {
            $idProfessor = (int) data_get($professorPerfil, 'id', 0);
            if ($idProfessor > 0) {
                $turmaProfessor = DB::table('turma')
                    ->where('id_professor', $idProfessor)
                    ->orderBy('nome_turma')
                    ->first();
            }
        }

        if (!$turmaProfessor && $nomeProfessor !== '' && Schema::hasColumn('turma', 'professor')) {
            $turmaProfessor = DB::table('turma')
                ->whereRaw('LOWER(TRIM(professor)) = ?', [mb_strtolower($nomeProfessor)])
                ->orderBy('nome_turma')
                ->first();
        }

        if ($turmaProfessor) {
            $colunasAluno = Schema::getColumnListing('aluno');
            $queryAlunosTurma = DB::table('aluno');

            if (in_array('turma_id', $colunasAluno, true)) {
                $queryAlunosTurma->where('turma_id', (int) data_get($turmaProfessor, 'id'));
            } elseif (in_array('turma', $colunasAluno, true)) {
                $queryAlunosTurma->where('turma', (string) data_get($turmaProfessor, 'nome_turma', ''));
            }

            $nomesAlunosTurma = $queryAlunosTurma
                ->when(
                    in_array('nome', $colunasAluno, true),
                    fn ($q) => $q->select('nome'),
                    fn ($q) => $q->selectRaw("'' as nome")
                )
                ->get()
                ->pluck('nome')
                ->map(fn ($nome) => trim((string) $nome))
                ->filter()
                ->values();

            if ($nomesAlunosTurma->isNotEmpty()) {
                $emailsAlunosDaTurma = DB::table('acesso_aluno')
                    ->whereIn('perfil_aluno', $nomesAlunosTurma)
                    ->when(
                        Schema::hasColumn('acesso_aluno', 'acesso'),
                        fn ($q) => $q->where('acesso', 'ativo')
                    )
                    ->pluck('email_encarregado')
                    ->map(fn ($email) => strtolower(trim((string) $email)))
                    ->filter()
                    ->unique()
                    ->values();
            }
        }
    }

    if ($emailsAlunosDaTurma->isNotEmpty()) {
        $contatos = $contatos
            ->map(function ($contato) use ($emailsAlunosDaTurma) {
                $email = strtolower(trim((string) data_get($contato, 'email', '')));
                $contato->prioridade_turma = $emailsAlunosDaTurma->contains($email) ? 1 : 0;
                return $contato;
            })
            ->sortByDesc('prioridade_turma')
            ->values()
            ->map(function ($contato) {
                unset($contato->prioridade_turma);
                return $contato;
            });
    }

    return view('telaprofessor_chat', compact('contatos', 'meuEmail'));
})->name('telaprofessor.chat');

Route::get('/telaprofessor/perfil', function () {
    $meuEmail = strtolower(trim((string) session('user_email', '')));
    $nomeSessao = trim((string) session('user_nome', ''));
    $loginSessao = trim((string) session('user_login', ''));
    $professorPerfil = null;

    if (Schema::hasTable('professor')) {
        if ($meuEmail !== '' && Schema::hasColumn('professor', 'email')) {
            $professorPerfil = DB::table('professor')
                ->whereRaw('LOWER(TRIM(email)) = ?', [$meuEmail])
                ->first();
        }

        if (!$professorPerfil && $nomeSessao !== '' && Schema::hasColumn('professor', 'nome')) {
            $professorPerfil = DB::table('professor')
                ->whereRaw('LOWER(TRIM(nome)) = ?', [mb_strtolower($nomeSessao)])
                ->first();
        }

        if (!$professorPerfil && $loginSessao !== '' && Schema::hasColumn('professor', 'nome')) {
            $professorPerfil = DB::table('professor')
                ->whereRaw('LOWER(TRIM(nome)) = ?', [mb_strtolower($loginSessao)])
                ->first();
        }
    }

    return view('telaprofessor', ['aba' => 'perfil', 'professorPerfil' => $professorPerfil]);
})->name('telaprofessor.perfil');

Route::get('/telaaluno', function () {
    return view('telaaluno');
})->name('telaaluno');

Route::get('/telaaluno/noticias', function () {
    $noticias = Schema::hasTable('noticias')
        ? DB::table('noticias')
            ->whereIn('destinatario', ['alunos', 'geral'])
            ->orderByDesc('data_publicacao')
            ->orderByDesc('hora_publicacao')
            ->orderByDesc('id')
            ->get()
        : collect();

    return view('telaaluno_noticias', compact('noticias'));
})->name('telaaluno.noticias');

Route::get('/telaaluno/chat', function () use ($chatUsuarioAutorizado, $chatListaContatos) {
    $meuEmail = (string) session('user_email', '');
    abort_unless($chatUsuarioAutorizado($meuEmail), 403, 'Acesso nao autorizado ao chat.');

    $contatos = $chatListaContatos($meuEmail);

    return view('telaaluno_chat', compact('contatos', 'meuEmail'));
})->name('telaaluno.chat');

Route::get('/chat/mensagens', function (\Illuminate\Http\Request $request) use ($chatUsuarioAutorizado) {
    $meuEmail = (string) session('user_email', '');
    abort_unless($chatUsuarioAutorizado($meuEmail), 403, 'Acesso nao autorizado ao chat.');

    $dados = $request->validate([
        'contato' => ['required', 'email'],
    ]);

    $contato = $dados['contato'];
    abort_unless($chatUsuarioAutorizado($contato), 403, 'Contato sem acesso ativo.');

    if (!Schema::hasTable('chat_mensagens')) {
        return response()->json(['mensagens' => []]);
    }

    $mensagens = DB::table('chat_mensagens')
        ->where(function ($query) use ($meuEmail, $contato) {
            $query->where('remetente_email', $meuEmail)
                ->where('destinatario_email', $contato);
        })
        ->orWhere(function ($query) use ($meuEmail, $contato) {
            $query->where('remetente_email', $contato)
                ->where('destinatario_email', $meuEmail);
        })
        ->orderBy('created_at')
        ->limit(200)
        ->get();

    return response()->json(['mensagens' => $mensagens]);
})->name('chat.fetch');

Route::post('/chat/mensagens', function (\Illuminate\Http\Request $request) use ($chatUsuarioAutorizado) {
    $meuEmail = (string) session('user_email', '');
    abort_unless($chatUsuarioAutorizado($meuEmail), 403, 'Acesso nao autorizado ao chat.');

    $dados = $request->validate([
        'destinatario_email' => ['required', 'email'],
        'mensagem' => ['required', 'string', 'max:1000'],
    ]);

    $destinatario = $dados['destinatario_email'];
    abort_unless($chatUsuarioAutorizado($destinatario), 403, 'Destinatario sem acesso ativo.');
    abort_unless(Schema::hasTable('chat_mensagens'), 500, 'Tabela de mensagens nao encontrada. Execute as migrations.');

    DB::table('chat_mensagens')->insert([
        'remetente_email' => $meuEmail,
        'destinatario_email' => $destinatario,
        'mensagem' => trim($dados['mensagem']),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['ok' => true]);
})->name('chat.send');

Route::get('/telaaluno/caderneta', function () {
    $meuEmail = (string) session('user_email', '');
    $perfilAcesso = null;
    $nomeAluno = null;
    $aluno = null;
    $colunaDesempenho = null;
    $colunaDescricao = null;
    $valorDesempenho = '-';
    $valorDescricao = '-';

    if (Schema::hasTable('acesso_aluno')) {
        $perfilAcesso = DB::table('acesso_aluno')
            ->where('email_encarregado', $meuEmail)
            ->first();
        $nomeAluno = trim((string) data_get($perfilAcesso, 'perfil_aluno', ''));
    }

    if (Schema::hasTable('aluno') && $nomeAluno !== '') {
        $aluno = DB::table('aluno')
            ->whereRaw('LOWER(TRIM(nome)) = ?', [mb_strtolower($nomeAluno)])
            ->first();

        $colunasAluno = Schema::getColumnListing('aluno');
        foreach (['desempenho', 'nota', 'nota_final', 'media'] as $coluna) {
            if (in_array($coluna, $colunasAluno, true)) {
                $colunaDesempenho = $coluna;
                break;
            }
        }
        foreach (['descricao', 'descricao_desempenho', 'observacao'] as $coluna) {
            if (in_array($coluna, $colunasAluno, true)) {
                $colunaDescricao = $coluna;
                break;
            }
        }

        if ($aluno) {
            if ($colunaDesempenho) {
                $valorDesempenho = (string) data_get($aluno, $colunaDesempenho, '-');
            }
            if ($colunaDescricao) {
                $valorDescricao = (string) data_get($aluno, $colunaDescricao, '-');
            }
        }
    }

    return view('telaaluno_caderneta', compact(
        'aluno',
        'nomeAluno',
        'meuEmail',
        'valorDesempenho',
        'valorDescricao'
    ));
})->name('telaaluno.caderneta');

Route::get('/telaaluno/perfil', function () {
    return view('telaaluno_perfil');
})->name('telaaluno.perfil');

Route::get('/dashboard', function () {
    $totalAlunos = Schema::hasTable('aluno')
        ? DB::table('aluno')->count()
        : 0;

    $totalProfessores = Schema::hasTable('professor')
        ? DB::table('professor')->count()
        : 0;

    $totalTurmas = Schema::hasTable('turmas')
        ? DB::table('turmas')->count()
        : 0;

    $totalFuncionarios = Schema::hasTable('funcionario')
        ? DB::table('funcionario')->count()
        : 0;

    return view('dashboard', compact('totalAlunos', 'totalProfessores', 'totalTurmas', 'totalFuncionarios'));
})->name('dashboard');

Route::get('/register', function () {
    return view('register');
})->name('register');

Route::get('/perfil', function () {
    $nomeFuncionario = trim((string) session('user_nome', ''));
    $emailUsuario = strtolower(trim((string) session('user_email', '')));
    $loginUsuario = trim((string) session('user_login', ''));
    $nivelAcesso = trim((string) session('access_level', ''));
    $perfilFuncionario = null;

    if (Schema::hasTable('funcionario')) {
        $normalizar = function (?string $valor): string {
            $valor = trim((string) $valor);
            if ($valor === '') {
                return '';
            }

            return preg_replace('/\s+/', ' ', mb_strtolower($valor)) ?? '';
        };

        $buscarFuncionarioCompativel = function () use ($nomeFuncionario, $emailUsuario, $loginUsuario, $normalizar) {
            $nomeNorm = $normalizar($nomeFuncionario);
            $emailNorm = $normalizar($emailUsuario);
            $loginNorm = $normalizar($loginUsuario);
            $candidatos = collect([$nomeNorm, $emailNorm, $loginNorm])
                ->filter(fn ($valor) => $valor !== '')
                ->unique()
                ->values();

            if ($candidatos->isEmpty()) {
                return null;
            }

            if (Schema::hasColumn('funcionario', 'nome')) {
                foreach ($candidatos as $candidato) {
                    $porNome = DB::table('funcionario')
                        ->whereRaw('LOWER(TRIM(nome)) = ?', [$candidato])
                        ->first();
                    if ($porNome) {
                        return $porNome;
                    }
                }
            }

            if (Schema::hasColumn('funcionario', 'email')) {
                foreach ($candidatos as $candidato) {
                    $porEmail = DB::table('funcionario')
                        ->whereRaw('LOWER(TRIM(email)) = ?', [$candidato])
                        ->first();
                    if ($porEmail) {
                        return $porEmail;
                    }
                }
            }

            if (Schema::hasColumn('funcionario', 'bi_passaporte')) {
                foreach ($candidatos as $candidato) {
                    $porBi = DB::table('funcionario')
                        ->whereRaw('LOWER(TRIM(bi_passaporte)) = ?', [$candidato])
                        ->first();
                    if ($porBi) {
                        return $porBi;
                    }
                }
            }

            if (!Schema::hasColumn('funcionario', 'nome')) {
                return null;
            }

            $funcionarios = DB::table('funcionario')->get();
            foreach ($funcionarios as $funcionario) {
                $nomeFuncNorm = $normalizar((string) data_get($funcionario, 'nome', ''));
                $emailFuncNorm = $normalizar((string) data_get($funcionario, 'email', ''));
                $biFuncNorm = $normalizar((string) data_get($funcionario, 'bi_passaporte', ''));

                foreach ($candidatos as $candidato) {
                    if ($candidato === $nomeFuncNorm || $candidato === $emailFuncNorm || $candidato === $biFuncNorm) {
                        return $funcionario;
                    }

                    if (mb_strlen($candidato) >= 4 && ($nomeFuncNorm !== '')) {
                        if (str_contains($nomeFuncNorm, $candidato) || str_contains($candidato, $nomeFuncNorm)) {
                            return $funcionario;
                        }
                    }
                }
            }

            return null;
        };

        $perfilFuncionario = $buscarFuncionarioCompativel();

        if ($perfilFuncionario && Schema::hasColumn('funcionario', 'nome')) {
            $nomeEncontrado = trim((string) data_get($perfilFuncionario, 'nome', ''));
            if ($nomeEncontrado !== '') {
                session(['user_nome' => $nomeEncontrado]);
            }
        }
    }

    $dadosUsuario = [
        'nome' => $nomeFuncionario !== '' ? $nomeFuncionario : ($loginUsuario !== '' && !filter_var($loginUsuario, FILTER_VALIDATE_EMAIL) ? $loginUsuario : '-'),
        'email' => $emailUsuario !== '' ? $emailUsuario : (filter_var($loginUsuario, FILTER_VALIDATE_EMAIL) ? $loginUsuario : '-'),
        'login' => $loginUsuario !== '' ? $loginUsuario : '-',
        'nivel_acesso' => $nivelAcesso !== '' ? $nivelAcesso : '-',
    ];

    if ($perfilFuncionario) {
        $nomeDoPerfil = trim((string) data_get($perfilFuncionario, 'nome', ''));
        $emailDoPerfil = trim((string) data_get($perfilFuncionario, 'email', ''));

        if ($nomeDoPerfil !== '') {
            $dadosUsuario['nome'] = $nomeDoPerfil;
        }
        if ($emailDoPerfil !== '') {
            $dadosUsuario['email'] = $emailDoPerfil;
        }
    }

    return view('perfil', compact('perfilFuncionario', 'dadosUsuario'));
})->name('perfil');

Route::get('/alunos', function () {
    $alunos = Schema::hasTable('aluno')
        ? DB::table('aluno')->orderBy('nome')->get()
        : collect();

    return view('alunos', compact('alunos'));
})->name('alunos');

Route::get('/professores', function () {
    $professores = Schema::hasTable('professor')
        ? DB::table('professor')->orderByDesc('id')->get()
        : collect();

    return view('professores', compact('professores'));
})->name('professores');

Route::get('/academia', function () {
    return view('academia');
})->name('academia');

Route::get('/academia/chat', function () use ($chatUsuarioAutorizado, $chatListaContatos) {
    $meuEmail = strtolower(trim((string) session('user_email', '')));
    $nivelAcesso = strtolower(trim((string) session('access_level', '')));

    if (str_contains($nivelAcesso, 'admin')) {
        abort_unless($chatUsuarioAutorizado($meuEmail), 403, 'Acesso nao autorizado ao chat.');
        $contatos = $chatListaContatos($meuEmail);
        return view('academia_chat', compact('contatos', 'meuEmail'));
    }

    if (str_contains($nivelAcesso, 'prof')) {
        return redirect()->route('telaprofessor.chat');
    }

    if (str_contains($nivelAcesso, 'aluno')) {
        return redirect()->route('telaaluno.chat');
    }

    if ($chatUsuarioAutorizado($meuEmail)) {
        $ehAdmin = Schema::hasTable('acesso_admin')
            ? (
                Schema::hasColumn('acesso_admin', 'email_encarregado')
                    ? DB::table('acesso_admin')
                        ->where('email_encarregado', $meuEmail)
                        ->when(
                            Schema::hasColumn('acesso_admin', 'acesso'),
                            fn ($query) => $query->where('acesso', 'ativo')
                        )
                        ->exists()
                    : DB::table('acesso_admin')
                        ->where('email', $meuEmail)
                        ->when(
                            Schema::hasColumn('acesso_admin', 'acesso'),
                            fn ($query) => $query->where('acesso', 'ativo')
                        )
                        ->exists()
            )
            : false;

        if ($ehAdmin) {
            $contatos = $chatListaContatos($meuEmail);
            return view('academia_chat', compact('contatos', 'meuEmail'));
        }

        $ehProfessor = Schema::hasTable('acesso_professor')
            ? DB::table('acesso_professor')
                ->where('email_encarregado', $meuEmail)
                ->when(
                    Schema::hasColumn('acesso_professor', 'acesso'),
                    fn ($query) => $query->where('acesso', 'ativo')
                )
                ->exists()
            : false;

        return $ehProfessor
            ? redirect()->route('telaprofessor.chat')
            : redirect()->route('telaaluno.chat');
    }

    return redirect()->route('academia')->with('error', 'Nao foi possivel abrir o chat para este utilizador.');
})->name('academia.chat');

Route::get('/academia/noticias', function () {
    $noticias = Schema::hasTable('noticias')
        ? DB::table('noticias')
            ->orderByDesc('data_publicacao')
            ->orderByDesc('hora_publicacao')
            ->orderByDesc('id')
            ->get()
        : collect();

    return view('academia_noticias', compact('noticias'));
})->name('academia.noticias');

Route::post('/academia/noticias', function (\Illuminate\Http\Request $request) {
    $dados = $request->validate([
        'assunto' => ['required', 'string', 'max:120'],
        'data' => ['required', 'date'],
        'hora' => ['required', 'date_format:H:i'],
        'destinatario' => ['required', Rule::in(['alunos', 'professores', 'geral'])],
        'nota' => ['required', 'string', 'max:2000'],
    ]);

    if (!Schema::hasTable('noticias')) {
        return redirect()->route('academia.noticias')->withErrors([
            'noticias' => 'A tabela de noticias nao existe. Executa as migracoes primeiro.',
        ])->withInput();
    }

    DB::table('noticias')->insert([
        'assunto' => trim((string) $dados['assunto']),
        'data_publicacao' => $dados['data'],
        'hora_publicacao' => $dados['hora'],
        'destinatario' => $dados['destinatario'],
        'nota' => trim((string) $dados['nota']),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('academia.noticias')->with('success', 'Noticia publicada com sucesso.');
})->name('academia.noticias.store');

Route::put('/academia/noticias/{id}', function (\Illuminate\Http\Request $request, int $id) {
    $dados = $request->validate([
        'assunto' => ['required', 'string', 'max:120'],
        'data' => ['required', 'date'],
        'hora' => ['required', 'date_format:H:i'],
        'destinatario' => ['required', Rule::in(['alunos', 'professores', 'geral'])],
        'nota' => ['required', 'string', 'max:2000'],
    ]);

    if (!Schema::hasTable('noticias')) {
        return redirect()->route('academia.noticias')->withErrors([
            'noticias' => 'A tabela de noticias nao existe. Executa as migracoes primeiro.',
        ])->withInput();
    }

    $existeNoticia = DB::table('noticias')->where('id', $id)->exists();
    if (!$existeNoticia) {
        return redirect()->route('academia.noticias')->withErrors([
            'noticias' => 'Noticia nao encontrada para edicao.',
        ]);
    }

    DB::table('noticias')->where('id', $id)->update([
        'assunto' => trim((string) $dados['assunto']),
        'data_publicacao' => $dados['data'],
        'hora_publicacao' => $dados['hora'],
        'destinatario' => $dados['destinatario'],
        'nota' => trim((string) $dados['nota']),
        'updated_at' => now(),
    ]);

    return redirect()->route('academia.noticias')->with('success', 'Noticia atualizada com sucesso.');
})->name('academia.noticias.update');

Route::delete('/academia/noticias/{id}', function (int $id) {
    if (!Schema::hasTable('noticias')) {
        return redirect()->route('academia.noticias')->withErrors([
            'noticias' => 'A tabela de noticias nao existe. Executa as migracoes primeiro.',
        ]);
    }

    $linhasAfetadas = DB::table('noticias')->where('id', $id)->delete();
    if ($linhasAfetadas === 0) {
        return redirect()->route('academia.noticias')->withErrors([
            'noticias' => 'Noticia nao encontrada para eliminacao.',
        ]);
    }

    return redirect()->route('academia.noticias')->with('success', 'Noticia eliminada com sucesso.');
})->name('academia.noticias.destroy');

Route::get('/academia/administradores', function (\Illuminate\Http\Request $request) {
    $q = trim((string) $request->query('q', ''));

    if (!Schema::hasTable('acesso_admin')) {
        return view('academia_administradores', ['admins' => collect(), 'q' => $q]);
    }

    $query = DB::table('acesso_admin');
    if ($q !== '') {
        $colunasPesquisa = collect(['email_encarregado', 'email', 'nome', 'acesso', 'perfil_admin', 'perfil', 'tipo', 'nivel'])
            ->filter(fn (string $coluna) => Schema::hasColumn('acesso_admin', $coluna))
            ->values();

        if ($colunasPesquisa->isNotEmpty()) {
            $query->where(function ($subQuery) use ($colunasPesquisa, $q) {
                foreach ($colunasPesquisa as $index => $coluna) {
                    if ($index === 0) {
                        $subQuery->where($coluna, 'like', "%{$q}%");
                    } else {
                        $subQuery->orWhere($coluna, 'like', "%{$q}%");
                    }
                }
            });
        }
    }

    $admins = $query->orderByDesc('id')->get();
    $funcionarios = Schema::hasTable('funcionario')
        ? DB::table('funcionario')->select('nome')->orderBy('nome')->get()
        : collect();

    return view('academia_administradores', compact('admins', 'q', 'funcionarios'));
})->name('academia.administradores');

Route::post('/academia/administradores', function (\Illuminate\Http\Request $request) {
    if (!Schema::hasTable('acesso_admin')) {
        return redirect()->route('academia.administradores')->withErrors([
            'administradores' => 'Tabela acesso_admin nao encontrada.',
        ])->withInput();
    }
    if (!Schema::hasTable('funcionario')) {
        return redirect()->route('academia.administradores')->withErrors([
            'administradores' => 'Tabela funcionario nao encontrada.',
        ])->withInput();
    }

    $dados = $request->validate([
        'nome_funcionario' => ['required', 'string', 'max:60'],
        'email_encarregado' => ['required', 'email', 'max:255'],
        'password' => ['required', 'string', 'min:6', 'max:255'],
        'acesso' => ['nullable', Rule::in(['ativo', 'inativo'])],
    ]);

    $nomeFuncionario = trim((string) $dados['nome_funcionario']);
    $funcionario = DB::table('funcionario')
        ->whereRaw('LOWER(TRIM(nome)) = ?', [mb_strtolower($nomeFuncionario)])
        ->first();

    if (!$funcionario) {
        return redirect()->route('academia.administradores')->withErrors([
            'administradores' => 'Funcionario nao encontrado. Informe um nome que exista na tabela funcionario.',
        ])->withInput();
    }

    $emailAdmin = strtolower(trim((string) $dados['email_encarregado']));

    $colunaEmailAdmin = collect(['email_encarregado', 'email'])
        ->first(fn (string $coluna) => Schema::hasColumn('acesso_admin', $coluna));

    if ($colunaEmailAdmin) {
        $adminExistente = DB::table('acesso_admin')
            ->whereRaw('LOWER(TRIM(' . $colunaEmailAdmin . ')) = ?', [mb_strtolower($emailAdmin)])
            ->exists();

        if ($adminExistente) {
            return redirect()->route('academia.administradores')->withErrors([
                'administradores' => 'Ja existe um administrador com este email.',
            ])->withInput();
        }
    }

    $definirSenhaParaColuna = function (string $coluna, string $senha): string {
        $metadata = DB::selectOne(
            "SELECT CHARACTER_MAXIMUM_LENGTH AS max_len
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = 'acesso_admin'
               AND COLUMN_NAME = ?",
            [$coluna]
        );

        $tamanhoMaximo = (int) data_get($metadata, 'max_len', 0);
        $senhaHash = Hash::make($senha);

        if ($tamanhoMaximo >= strlen($senhaHash)) {
            return $senhaHash;
        }

        return $senha;
    };

    $payload = [];
    if (Schema::hasColumn('acesso_admin', 'id')) {
        $payload['id'] = (int) data_get($funcionario, 'id');
    }

    if (Schema::hasColumn('acesso_admin', 'email_encarregado')) {
        $payload['email_encarregado'] = $emailAdmin;
    } elseif (Schema::hasColumn('acesso_admin', 'email')) {
        $payload['email'] = $emailAdmin;
    }
    if (Schema::hasColumn('acesso_admin', 'password')) {
        $payload['password'] = $definirSenhaParaColuna('password', (string) $dados['password']);
    } elseif (Schema::hasColumn('acesso_admin', 'palavrapasse')) {
        $payload['palavrapasse'] = $definirSenhaParaColuna('palavrapasse', (string) $dados['password']);
    } elseif (Schema::hasColumn('acesso_admin', 'senha')) {
        $payload['senha'] = $definirSenhaParaColuna('senha', (string) $dados['password']);
    } elseif (Schema::hasColumn('acesso_admin', 'passwork')) {
        $payload['passwork'] = $definirSenhaParaColuna('passwork', (string) $dados['password']);
    }

    if (Schema::hasColumn('acesso_admin', 'acesso')) {
        $payload['acesso'] = (string) ($dados['acesso'] ?? 'ativo');
    }
    if (Schema::hasColumn('acesso_admin', 'nome')) {
        $payload['nome'] = (string) data_get($funcionario, 'nome');
    }

    if (Schema::hasColumn('acesso_admin', 'perfil_admin')) {
        $payload['perfil_admin'] = 'administrador';
    } elseif (Schema::hasColumn('acesso_admin', 'perfil')) {
        $payload['perfil'] = 'administrador';
    } elseif (Schema::hasColumn('acesso_admin', 'tipo')) {
        $payload['tipo'] = 'admin';
    } elseif (Schema::hasColumn('acesso_admin', 'nivel')) {
        $payload['nivel'] = 'admin';
    }

    if (Schema::hasColumn('acesso_admin', 'created_at')) {
        $payload['created_at'] = now();
    }
    if (Schema::hasColumn('acesso_admin', 'updated_at')) {
        $payload['updated_at'] = now();
    }

    if (empty($payload)) {
        return redirect()->route('academia.administradores')->withErrors([
            'administradores' => 'A tabela acesso_admin nao possui colunas compativeis para guardar o formulario.',
        ])->withInput();
    }

    $adminMesmoFuncionario = Schema::hasColumn('acesso_admin', 'id')
        ? DB::table('acesso_admin')->where('id', (int) data_get($funcionario, 'id'))->exists()
        : false;

    if ($adminMesmoFuncionario) {
        return redirect()->route('academia.administradores')->withErrors([
            'administradores' => 'Este funcionario ja possui acesso de administrador.',
        ])->withInput();
    }

    DB::table('acesso_admin')->insert($payload);

    return redirect()->route('academia.administradores')->with('success', 'Administrador adicionado com sucesso.');
})->name('academia.administradores.store');

Route::delete('/academia/administradores/{id}', function (int $id) {
    if (!Schema::hasTable('acesso_admin')) {
        return redirect()->route('academia.administradores')->withErrors([
            'administradores' => 'Tabela acesso_admin nao encontrada.',
        ]);
    }

    DB::table('acesso_admin')->where('id', $id)->delete();

    return redirect()->route('academia.administradores')->with('success', 'Administrador removido com sucesso.');
})->whereNumber('id')->name('academia.administradores.destroy');

Route::get('/academia/acesso-alunos', function (\Illuminate\Http\Request $request) {
    $q = trim((string) $request->query('q', ''));

    if (!Schema::hasTable('acesso_aluno')) {
        return view('academia_acesso_alunos', ['alunos' => collect(), 'q' => $q]);
    }

    $query = DB::table('acesso_aluno');
    if ($q !== '') {
        $query->where(function ($subQuery) use ($q) {
            $subQuery->where('email_encarregado', 'like', "%{$q}%");

            if (Schema::hasColumn('acesso_aluno', 'acesso')) {
                $subQuery->orWhere('acesso', 'like', "%{$q}%");
            }
        });
    }

    $alunos = $query->orderByDesc('id')->get();

    return view('academia_acesso_alunos', compact('alunos', 'q'));
})->name('academia.acesso_alunos');

Route::get('/academia/acesso-alunos/criar', function () {
    $perfisAluno = Schema::hasTable('aluno')
        ? DB::table('aluno')->select('nome')->orderBy('nome')->get()
        : collect();

    return view('academia_acesso_alunos_form', [
        'isEdit' => false,
        'aluno' => null,
        'perfisAluno' => $perfisAluno,
    ]);
})->name('academia.acesso_alunos.create');

Route::post('/academia/acesso-alunos', function (\Illuminate\Http\Request $request) {
    $regras = [
        'email_encarregado' => ['required', 'email', 'max:255', 'unique:acesso_aluno,email_encarregado'],
        'password' => ['required', 'string', 'min:6'],
        'perfil_aluno' => ['nullable', 'string', 'max:60'],
    ];
    if (Schema::hasTable('aluno')) {
        $regras['perfil_aluno'][] = Rule::exists('aluno', 'nome');
    }
    $dados = $request->validate($regras);

    DB::table('acesso_aluno')->insert([
        'email_encarregado' => $dados['email_encarregado'],
        'password' => Hash::make($dados['password']),
        'perfil_aluno' => !empty($dados['perfil_aluno']) ? trim((string) $dados['perfil_aluno']) : null,
        'acesso' => 'ativo',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('academia.acesso_alunos')->with('success', 'Acesso de aluno criado com sucesso.');
})->name('academia.acesso_alunos.store');

Route::get('/academia/acesso-alunos/{id}/editar', function (int $id) {
    $aluno = DB::table('acesso_aluno')->where('id', $id)->first();
    abort_if(!$aluno, 404);

    $perfisAluno = Schema::hasTable('aluno')
        ? DB::table('aluno')->select('nome')->orderBy('nome')->get()
        : collect();

    return view('academia_acesso_alunos_form', [
        'isEdit' => true,
        'aluno' => $aluno,
        'perfisAluno' => $perfisAluno,
    ]);
})->name('academia.acesso_alunos.edit');

Route::put('/academia/acesso-alunos/{id}', function (\Illuminate\Http\Request $request, int $id) {
    $aluno = DB::table('acesso_aluno')->where('id', $id)->first();
    abort_if(!$aluno, 404);

    $regras = [
        'email_encarregado' => ['required', 'email', 'max:255', Rule::unique('acesso_aluno', 'email_encarregado')->ignore($id)],
        'acesso' => ['required', 'in:ativo,inativo'],
        'password' => ['nullable', 'string', 'min:6'],
        'perfil_aluno' => ['nullable', 'string', 'max:60'],
    ];
    if (Schema::hasTable('aluno')) {
        $regras['perfil_aluno'][] = Rule::exists('aluno', 'nome');
    }
    $dados = $request->validate($regras);

    $update = [
        'email_encarregado' => $dados['email_encarregado'],
        'acesso' => $dados['acesso'],
        'perfil_aluno' => !empty($dados['perfil_aluno']) ? trim((string) $dados['perfil_aluno']) : null,
        'updated_at' => now(),
    ];
    if (!empty($dados['password'])) {
        $update['password'] = Hash::make($dados['password']);
    }

    DB::table('acesso_aluno')->where('id', $id)->update($update);

    return redirect()->route('academia.acesso_alunos')->with('success', 'Acesso de aluno editado com sucesso.');
})->name('academia.acesso_alunos.update');

Route::delete('/academia/acesso-alunos/{id}', function (int $id) {
    DB::table('acesso_aluno')->where('id', $id)->delete();

    return redirect()->route('academia.acesso_alunos')->with('success', 'Acesso de aluno removido com sucesso.');
})->name('academia.acesso_alunos.destroy');

Route::get('/academia/turmas', function () use ($obterListaProfessores, $obterTabelaTurmas) {
    $q = trim((string) request()->query('q', ''));
    $professoresLookup = $obterListaProfessores();
    $tabelaTurmas = $obterTabelaTurmas();

    if (!$tabelaTurmas) {
        return view('academia_turmas', ['turmas' => collect(), 'q' => $q, 'professoresLookup' => $professoresLookup]);
    }

    $query = DB::table($tabelaTurmas);
    if ($q !== '') {
        $query->where('nome_turma', 'like', "%{$q}%");
    }

    $turmas = $query->orderBy('nome_turma')->get();

    return view('academia_turmas', compact('turmas', 'q', 'professoresLookup'));
})->name('academia.turmas');

Route::post('/academia/turmas', function (\Illuminate\Http\Request $request) use ($obterTabelaTurmas, $obterColunaProfessorTurma, $obterProximoIdTabela) {
    $dados = $request->validate([
        'nome_turma' => ['required', 'string', 'max:35'],
        'idade_alunos' => ['nullable', 'string', 'max:20'],
        'professor' => ['required', 'string', 'max:60'],
        'professor_id' => ['nullable', 'integer'],
        'id_professor' => ['nullable', 'integer'],
        'professor_auxiliar' => ['nullable', 'string', 'max:60'],
        'tempo_aula' => ['nullable', 'date_format:H:i'],
        'turma_id' => ['nullable', 'integer'],
    ]);

    $tabelaTurmas = $obterTabelaTurmas();
    if (!$tabelaTurmas) {
        return redirect()->route('academia.turmas')
            ->withInput()
            ->with('error', 'Nao foi possivel guardar. Nenhuma tabela de turmas foi encontrada (turma/turmas).');
    }

    $colunaProfessorTurma = $obterColunaProfessorTurma($tabelaTurmas);

    $idProfessor = null;
    if (Schema::hasTable('professor')) {
        $professorEncontrado = DB::table('professor')
            ->whereRaw('LOWER(TRIM(nome)) = ?', [mb_strtolower(trim($dados['professor']))])
            ->first();
        $idProfessor = data_get($professorEncontrado, 'id');
    }

    if (!$idProfessor && !empty($dados['professor_id'])) {
        $idProfessor = (int) $dados['professor_id'];
    }

    if (!$idProfessor && !empty($dados['id_professor'])) {
        $idProfessor = (int) $dados['id_professor'];
    }

    if (!$idProfessor) {
        return redirect()->route('academia.turmas')
            ->withInput()
            ->with('error', 'Selecione um professor valido para preencher professor_id.');
    }

    $payload = [
        'nome_turma' => $dados['nome_turma'],
        'idade_alunos' => $dados['idade_alunos'] ?? null,
        'professor' => $dados['professor'],
        'professor_auxiliar' => $dados['professor_auxiliar'] ?? null,
        'created_at' => now(),
        'updated_at' => now(),
    ];

    if (Schema::hasColumn($tabelaTurmas, 'tempo_aula')) {
        $payload['tempo_aula'] = !empty($dados['tempo_aula']) ? ($dados['tempo_aula'] . ':00') : null;
    }

    if (Schema::hasColumn($tabelaTurmas, 'tempo_aula_diaria')) {
        $payload['tempo_aula_diaria'] = !empty($dados['tempo_aula']) ? $dados['tempo_aula'] : null;
    }

    if (Schema::hasColumn($tabelaTurmas, 'tempo_aula_diario')) {
        $payload['tempo_aula_diario'] = !empty($dados['tempo_aula']) ? $dados['tempo_aula'] : null;
    }

    if ($colunaProfessorTurma) {
        $payload[$colunaProfessorTurma] = $idProfessor;
    }

    $usaTurmaId = Schema::hasColumn($tabelaTurmas, 'turma_id');

    if ($usaTurmaId) {
        if (!empty($dados['turma_id'])) {
            $payload['turma_id'] = (int) $dados['turma_id'];
        } else {
            $proximoIdTurma = $obterProximoIdTabela($tabelaTurmas);
            $payload['id'] = $proximoIdTurma;
            $payload['turma_id'] = $proximoIdTurma;
        }
    }

    $idInserido = !empty($payload['id'])
        ? (DB::table($tabelaTurmas)->insert($payload) ? (int) $payload['id'] : 0)
        : DB::table($tabelaTurmas)->insertGetId($payload);

    if (
        $usaTurmaId
        && !array_key_exists('turma_id', $payload)
        && $idInserido > 0
    ) {
        DB::table($tabelaTurmas)->where('id', $idInserido)->update(['turma_id' => $idInserido]);
    }

    return redirect()->route('academia.turmas')->with('success', 'Sala criada com sucesso.');
})->name('academia.turmas.store');

Route::post('/academia/turmas/editar', function (\Illuminate\Http\Request $request) use ($obterListaProfessores, $obterTabelaTurmas) {
    $dados = $request->validate([
        'turma_id' => ['required', 'integer'],
    ]);

    $tabelaTurmas = $obterTabelaTurmas();
    abort_if(!$tabelaTurmas, 404);

    $turma = DB::table($tabelaTurmas)->where('id', $dados['turma_id'])->first();
    abort_if(!$turma, 404);

    $professoresLookup = $obterListaProfessores();

    return view('academia_turmas_edit', compact('turma', 'professoresLookup'));
})->name('academia.turmas.edit');

Route::post('/academia/turmas/{id}', function (\Illuminate\Http\Request $request, int $id) use ($obterTabelaTurmas, $obterColunaProfessorTurma) {
    $tabelaTurmas = $obterTabelaTurmas();
    abort_if(!$tabelaTurmas, 404);

    $turma = DB::table($tabelaTurmas)->where('id', $id)->first();
    abort_if(!$turma, 404);

    $dados = $request->validate([
        'nome_turma' => ['required', 'string', 'max:80'],
        'idade_alunos' => ['nullable', 'string', 'max:30'],
        'professor' => ['required', 'string', 'max:100'],
        'id_professor' => ['nullable', 'integer'],
        'professor_auxiliar' => ['nullable', 'string', 'max:100'],
        'tempo_aula_diaria' => ['nullable', 'string', 'max:40'],
    ]);

    $colunaProfessorTurma = $obterColunaProfessorTurma($tabelaTurmas);

    $idProfessor = null;
    if (Schema::hasTable('professor')) {
        $professorEncontrado = DB::table('professor')
            ->whereRaw('LOWER(TRIM(nome)) = ?', [mb_strtolower(trim($dados['professor']))])
            ->first();
        $idProfessor = data_get($professorEncontrado, 'id');
    }

    if (!$idProfessor && !empty($dados['id_professor'])) {
        $idProfessor = (int) $dados['id_professor'];
    }

    $payload = [
        'nome_turma' => $dados['nome_turma'],
        'idade_alunos' => $dados['idade_alunos'] ?? null,
        'professor' => $dados['professor'],
        'professor_auxiliar' => $dados['professor_auxiliar'] ?? null,
        'tempo_aula_diaria' => $dados['tempo_aula_diaria'] ?? null,
        'updated_at' => now(),
    ];

    if ($colunaProfessorTurma) {
        $payload[$colunaProfessorTurma] = $idProfessor;
    }

    DB::table($tabelaTurmas)->where('id', $id)->update($payload);

    return redirect()->route('academia.turmas')->with('success', 'Turma atualizada com sucesso.');
})->whereNumber('id')->name('academia.turmas.update');

Route::delete('/academia/turmas/{id}', function (int $id) {
    abort_if(!Schema::hasTable('turmas'), 404);

    $turma = DB::table('turmas')->where('id', $id)->first();
    abort_if(!$turma, 404);

    DB::transaction(function () use ($id, $turma) {
        if (Schema::hasTable('aluno')) {
            $colunasAluno = Schema::getColumnListing('aluno');

            if (in_array('turma_id', $colunasAluno, true)) {
                DB::table('aluno')
                    ->where('turma_id', $id)
                    ->update(['turma_id' => null]);
            }

            if (in_array('turma', $colunasAluno, true)) {
                DB::table('aluno')
                    ->where('turma', (string) data_get($turma, 'nome_turma', ''))
                    ->update(['turma' => null]);
            }
        }

        DB::table('turmas')->where('id', $id)->delete();
    });

    return redirect()->route('academia.turmas')->with('success', 'Turma eliminada com sucesso.');
})->whereNumber('id')->name('academia.turmas.destroy');

Route::post('/academia/turmas/lista', function (\Illuminate\Http\Request $request) {
    $dados = $request->validate([
        'turma_id' => ['required', 'integer'],
    ]);

    return redirect()->route('academia.turmas.lista', ['id' => $dados['turma_id']]);
})->name('academia.turmas.lista.select');

Route::get('/academia/turmas/lista/{id}', function (int $id) {
    return redirect()->route('academia.turmas.lista', ['id' => $id]);
})->name('academia.turmas.lista.direct');

Route::get('/academia/turmas/{id}/lista', function (int $id) use ($obterTabelaTurmas) {
    $tabelaTurmas = $obterTabelaTurmas();
    abort_if(!$tabelaTurmas, 404);
    $turma = DB::table($tabelaTurmas)->where('id', $id)->first();
    abort_if(!$turma, 404);

    $colunasAluno = Schema::hasTable('aluno') ? Schema::getColumnListing('aluno') : [];
    $colunaLigacao = null;
    if (in_array('id_turma', $colunasAluno, true)) {
        $colunaLigacao = 'id_turma';
    } elseif (in_array('turma_id', $colunasAluno, true)) {
        $colunaLigacao = 'turma_id';
    } elseif (in_array('turma', $colunasAluno, true)) {
        $colunaLigacao = 'turma';
    }

    $colunaReferenciaAluno = null;
    foreach (['id', 'bi', 'email', 'nome'] as $coluna) {
        if (in_array($coluna, $colunasAluno, true)) {
            $colunaReferenciaAluno = $coluna;
            break;
        }
    }

    $colunaDesempenho = null;
    foreach (['desempenho', 'nota', 'nota_final', 'media'] as $coluna) {
        if (in_array($coluna, $colunasAluno, true)) {
            $colunaDesempenho = $coluna;
            break;
        }
    }

    $colunaDescricao = null;
    foreach (['descricao', 'descricao_desempenho', 'observacao'] as $coluna) {
        if (in_array($coluna, $colunasAluno, true)) {
            $colunaDescricao = $coluna;
            break;
        }
    }

    $alunosTurma = collect();
    $alunosDisponiveis = collect();

    if (Schema::hasTable('aluno') && $colunaLigacao) {
        if ($colunaLigacao === 'id_turma') {
            $alunosTurma = DB::table('aluno')->where('id_turma', $id)->orderBy('nome')->get();
            $alunosDisponiveis = DB::table('aluno')
                ->where(function ($query) {
                    $query->whereNull('id_turma')->orWhere('id_turma', 0);
                })
                ->orderBy('nome')
                ->get();
        } elseif ($colunaLigacao === 'turma_id') {
            $alunosTurma = DB::table('aluno')->where('turma_id', $id)->orderBy('nome')->get();
            $alunosDisponiveis = DB::table('aluno')->whereNull('turma_id')->orderBy('nome')->get();
        } else {
            $nomeTurma = (string) data_get($turma, 'nome_turma', '');
            $alunosTurma = DB::table('aluno')->where('turma', $nomeTurma)->orderBy('nome')->get();
            $alunosDisponiveis = DB::table('aluno')
                ->where(function ($query) {
                    $query->whereNull('turma')->orWhere('turma', '');
                })
                ->orderBy('nome')
                ->get();
        }
    }

    return view('academia_turmas_lista', compact(
        'turma',
        'alunosTurma',
        'alunosDisponiveis',
        'colunaLigacao',
        'colunaReferenciaAluno',
        'colunaDesempenho',
        'colunaDescricao'
    ));
})->whereNumber('id')->name('academia.turmas.lista');

Route::get('/academia/turmas/{id}/alunos/pesquisar', function (\Illuminate\Http\Request $request, int $id) {
    abort_if(!Schema::hasTable('turmas'), 404);
    $turma = DB::table('turmas')->where('id', $id)->first();
    abort_if(!$turma, 404);

    if (!Schema::hasTable('aluno')) {
        return response()->json(['alunos' => []]);
    }

    $q = trim((string) $request->query('q', ''));
    if ($q === '') {
        return response()->json(['alunos' => []]);
    }

    $colunasAluno = Schema::getColumnListing('aluno');
    $colunaLigacao = in_array('turma', $colunasAluno, true) ? 'turma' : (in_array('turma_id', $colunasAluno, true) ? 'turma_id' : null);

    $colunaReferenciaAluno = null;
    foreach (['id', 'bi', 'email', 'nome'] as $coluna) {
        if (in_array($coluna, $colunasAluno, true)) {
            $colunaReferenciaAluno = $coluna;
            break;
        }
    }

    if (!in_array('nome', $colunasAluno, true) || !$colunaReferenciaAluno) {
        return response()->json(['alunos' => []]);
    }

    $query = DB::table('aluno')->where('nome', 'like', "%{$q}%");

    if ($colunaLigacao === 'turma_id') {
        $query->whereNull('turma_id');
    } elseif ($colunaLigacao === 'turma') {
        $query->where(function ($subQuery) {
            $subQuery->whereNull('turma')->orWhere('turma', '');
        });
    }

    $alunos = $query->orderBy('nome')->limit(20)->get()->map(function ($aluno) use ($colunaReferenciaAluno) {
        return [
            'nome' => (string) data_get($aluno, 'nome', ''),
            'referencia' => (string) data_get($aluno, $colunaReferenciaAluno, ''),
        ];
    })->values();

    return response()->json(['alunos' => $alunos]);
})->whereNumber('id')->name('academia.turmas.alunos.pesquisar');

Route::post('/academia/turmas/{id}/alunos/adicionar', function (\Illuminate\Http\Request $request, int $id) {
    abort_if(!Schema::hasTable('turmas'), 404);
    $turma = DB::table('turmas')->where('id', $id)->first();
    abort_if(!$turma, 404);

    $dados = $request->validate([
        'aluno_ref' => ['required', 'string'],
    ]);

    if (!Schema::hasTable('aluno')) {
        return redirect()->route('academia.turmas.lista', ['id' => $id])->with('error', 'Tabela de alunos nao encontrada.');
    }

    $colunasAluno = Schema::getColumnListing('aluno');
    $colunaLigacao = in_array('turma', $colunasAluno, true) ? 'turma' : (in_array('turma_id', $colunasAluno, true) ? 'turma_id' : null);

    $colunaReferenciaAluno = null;
    foreach (['id', 'bi', 'email', 'nome'] as $coluna) {
        if (in_array($coluna, $colunasAluno, true)) {
            $colunaReferenciaAluno = $coluna;
            break;
        }
    }

    if (!$colunaLigacao || !$colunaReferenciaAluno) {
        return redirect()->route('academia.turmas.lista', ['id' => $id])->with('error', 'Estrutura da tabela aluno nao suporta vinculo de turma.');
    }

    $aluno = DB::table('aluno')->where($colunaReferenciaAluno, $dados['aluno_ref'])->first();
    if (!$aluno) {
        return redirect()->route('academia.turmas.lista', ['id' => $id])->with('error', 'Aluno selecionado nao encontrado.');
    }

    $valorLigacao = $colunaLigacao === 'turma_id' ? $id : data_get($turma, 'nome_turma');
    DB::table('aluno')->where($colunaReferenciaAluno, $dados['aluno_ref'])->update([$colunaLigacao => $valorLigacao]);

    return redirect()->route('academia.turmas.lista', ['id' => $id])->with('success', 'Aluno adicionado a turma.');
})->whereNumber('id')->name('academia.turmas.alunos.adicionar');

Route::post('/academia/turmas/{id}/alunos/remover', function (\Illuminate\Http\Request $request, int $id) {
    abort_if(!Schema::hasTable('turmas'), 404);
    $turma = DB::table('turmas')->where('id', $id)->first();
    abort_if(!$turma, 404);

    $dados = $request->validate([
        'aluno_ref' => ['required', 'string'],
    ]);

    if (!Schema::hasTable('aluno')) {
        return redirect()->route('academia.turmas.lista', ['id' => $id])->with('error', 'Tabela de alunos nao encontrada.');
    }

    $colunasAluno = Schema::getColumnListing('aluno');
    $colunaLigacao = in_array('turma', $colunasAluno, true) ? 'turma' : (in_array('turma_id', $colunasAluno, true) ? 'turma_id' : null);

    $colunaReferenciaAluno = null;
    foreach (['id', 'bi', 'email', 'nome'] as $coluna) {
        if (in_array($coluna, $colunasAluno, true)) {
            $colunaReferenciaAluno = $coluna;
            break;
        }
    }

    if (!$colunaLigacao || !$colunaReferenciaAluno) {
        return redirect()->route('academia.turmas.lista', ['id' => $id])->with('error', 'Estrutura da tabela aluno nao suporta vinculo de turma.');
    }

    $aluno = DB::table('aluno')->where($colunaReferenciaAluno, $dados['aluno_ref'])->first();
    if (!$aluno) {
        return redirect()->route('academia.turmas.lista', ['id' => $id])->with('error', 'Aluno selecionado nao encontrado.');
    }

    DB::table('aluno')->where($colunaReferenciaAluno, $dados['aluno_ref'])->update([$colunaLigacao => null]);

    return redirect()->route('academia.turmas.lista', ['id' => $id])->with('success', 'Aluno removido da turma.');
})->whereNumber('id')->name('academia.turmas.alunos.remover');

Route::post('/academia/turmas/{id}/alunos/notas', function (\Illuminate\Http\Request $request, int $id) {
    abort_if(!Schema::hasTable('turmas'), 404);
    $turma = DB::table('turmas')->where('id', $id)->first();
    abort_if(!$turma, 404);

    $dados = $request->validate([
        'aluno_ref' => ['required', 'string'],
        'desempenho' => ['required', 'numeric', 'between:0,20'],
        'descricao' => ['nullable', 'string', 'max:255'],
    ]);

    if (!Schema::hasTable('aluno')) {
        return redirect()->route('academia.turmas.lista', ['id' => $id])->with('error', 'Tabela de alunos nao encontrada.');
    }

    $colunasAluno = Schema::getColumnListing('aluno');
    $colunaLigacao = in_array('turma', $colunasAluno, true) ? 'turma' : (in_array('turma_id', $colunasAluno, true) ? 'turma_id' : null);

    $colunaReferenciaAluno = null;
    foreach (['id', 'bi', 'email', 'nome'] as $coluna) {
        if (in_array($coluna, $colunasAluno, true)) {
            $colunaReferenciaAluno = $coluna;
            break;
        }
    }

    $temColunaDesempenho = in_array('desempenho', $colunasAluno, true);
    $temColunaDescricao = in_array('descricao', $colunasAluno, true);

    if (!$colunaReferenciaAluno || !$temColunaDesempenho || !$temColunaDescricao) {
        return redirect()->route('academia.turmas.lista', ['id' => $id])->with('error', 'A tabela aluno precisa das colunas `desempenho` e `descricao`.');
    }

    $alunoQuery = DB::table('aluno')->where($colunaReferenciaAluno, $dados['aluno_ref']);
    if ($colunaLigacao === 'turma_id') {
        $alunoQuery->where('turma_id', $id);
    } elseif ($colunaLigacao === 'turma') {
        $alunoQuery->where('turma', data_get($turma, 'nome_turma'));
    }

    $aluno = $alunoQuery->first();
    if (!$aluno) {
        return redirect()->route('academia.turmas.lista', ['id' => $id])->with('error', 'Aluno nao encontrado nesta turma.');
    }

    $payload = [
        'desempenho' => $dados['desempenho'],
        'descricao' => isset($dados['descricao']) ? trim((string) $dados['descricao']) : null,
    ];

    DB::table('aluno')
        ->where($colunaReferenciaAluno, $dados['aluno_ref'])
        ->update($payload);

    return redirect()->route('academia.turmas.lista', ['id' => $id])->with('success', 'Desempenho atualizado com sucesso.');
})->whereNumber('id')->name('academia.turmas.alunos.notas');

Route::get('/academia/acesso-professores', function (\Illuminate\Http\Request $request) {
    $q = trim((string) $request->query('q', ''));

    if (!Schema::hasTable('acesso_professor')) {
        return view('academia_acesso_professores', ['professores' => collect(), 'q' => $q]);
    }

    $query = DB::table('acesso_professor');
    if ($q !== '') {
        $query->where(function ($subQuery) use ($q) {
            $subQuery->where('email_encarregado', 'like', "%{$q}%");

            if (Schema::hasColumn('acesso_professor', 'acesso')) {
                $subQuery->orWhere('acesso', 'like', "%{$q}%");
            }
        });
    }

    $professores = $query->orderByDesc('id')->get();

    return view('academia_acesso_professores', compact('professores', 'q'));
})->name('academia.acesso_professores');

Route::get('/academia/acesso-professores/criar', function () {
    $perfisProfessor = Schema::hasTable('professor')
        ? DB::table('professor')->select('nome')->orderBy('nome')->get()
        : collect();

    return view('academia_acesso_professores_form', [
        'isEdit' => false,
        'professor' => null,
        'perfisProfessor' => $perfisProfessor,
    ]);
})->name('academia.acesso_professores.create');

Route::post('/academia/acesso-professores', function (\Illuminate\Http\Request $request) {
    if (!$request->filled('email_encarregado') && $request->filled('email_professor')) {
        $request->merge([
            'email_encarregado' => $request->input('email_professor'),
        ]);
    }

    $dados = $request->validate([
        'email_encarregado' => ['required', 'email', 'max:255', 'unique:acesso_professor,email_encarregado'],
        'password' => ['required', 'string', 'min:6'],
    ]);

    DB::table('acesso_professor')->insert([
        'email_encarregado' => $dados['email_encarregado'],
        'password' => Hash::make($dados['password']),
        'acesso' => 'ativo',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return redirect()->route('academia.acesso_professores')->with('success', 'Acesso de professor criado com sucesso.');
})->name('academia.acesso_professores.store');

Route::get('/academia/acesso-professores/{id}/editar', function (int $id) {
    $professor = DB::table('acesso_professor')->where('id', $id)->first();
    abort_if(!$professor, 404);

    $perfisProfessor = Schema::hasTable('professor')
        ? DB::table('professor')->select('nome')->orderBy('nome')->get()
        : collect();

    return view('academia_acesso_professores_form', [
        'isEdit' => true,
        'professor' => $professor,
        'perfisProfessor' => $perfisProfessor,
    ]);
})->name('academia.acesso_professores.edit');

Route::put('/academia/acesso-professores/{id}', function (\Illuminate\Http\Request $request, int $id) {
    $professor = DB::table('acesso_professor')->where('id', $id)->first();
    abort_if(!$professor, 404);

    $dados = $request->validate([
        'email_encarregado' => ['required', 'email', 'max:255', Rule::unique('acesso_professor', 'email_encarregado')->ignore($id)],
        'acesso' => ['required', 'in:ativo,inativo'],
        'password' => ['nullable', 'string', 'min:6'],
    ]);

    $update = [
        'email_encarregado' => $dados['email_encarregado'],
        'acesso' => $dados['acesso'],
        'updated_at' => now(),
    ];
    if (!empty($dados['password'])) {
        $update['password'] = Hash::make($dados['password']);
    }

    DB::table('acesso_professor')->where('id', $id)->update($update);

    return redirect()->route('academia.acesso_professores')->with('success', 'Acesso de professor editado com sucesso.');
})->name('academia.acesso_professores.update');

Route::delete('/academia/acesso-professores/{id}', function (int $id) {
    DB::table('acesso_professor')->where('id', $id)->delete();

    return redirect()->route('academia.acesso_professores')->with('success', 'Acesso de professor removido com sucesso.');
})->name('academia.acesso_professores.destroy');


Route::get('/database', function (\Illuminate\Http\Request $request) {
    $q = trim((string) $request->query('q', ''));
    $lista = strtolower(trim((string) $request->query('lista', '')));
    $resultados = collect();
    $listaRegistros = collect();
    $listaTitulo = null;

    if (in_array($lista, ['aluno', 'professor', 'funcionario'], true)) {
        if ($lista === 'aluno' && Schema::hasTable('aluno')) {
            $listaTitulo = 'Lista de Alunos';
            $listaRegistros = DB::table('aluno')
                ->orderBy('nome')
                ->get()
                ->map(function ($aluno) {
                    return [
                        'tipo' => 'Aluno',
                        'tipo_key' => 'aluno',
                        'id' => data_get($aluno, 'bi'),
                        'nome' => data_get($aluno, 'nome'),
                        'dados' => (array) $aluno,
                    ];
                });
        }

        if ($lista === 'professor' && Schema::hasTable('professor')) {
            $listaTitulo = 'Lista de Professores';
            $listaRegistros = DB::table('professor')
                ->orderBy('nome')
                ->get()
                ->map(function ($professor) {
                    return [
                        'tipo' => 'Professor',
                        'tipo_key' => 'professor',
                        'id' => (string) data_get($professor, 'id'),
                        'nome' => data_get($professor, 'nome'),
                        'dados' => (array) $professor,
                    ];
                });
        }

        if ($lista === 'funcionario' && Schema::hasTable('funcionario')) {
            $listaTitulo = 'Lista de Funcionarios';
            $listaRegistros = DB::table('funcionario')
                ->orderBy('nome')
                ->get()
                ->map(function ($funcionario) {
                    return [
                        'tipo' => 'Funcionario',
                        'tipo_key' => 'funcionario',
                        'id' => data_get($funcionario, 'bi_passaporte'),
                        'nome' => data_get($funcionario, 'nome'),
                        'dados' => (array) $funcionario,
                    ];
                });
        }
    }

    if ($q !== '') {
        if (Schema::hasTable('aluno')) {
            $alunos = DB::table('aluno')
                ->where('nome', 'like', "%{$q}%")
                ->orderBy('nome')
                ->get()
                ->map(function ($aluno) {
                    return [
                        'tipo' => 'Aluno',
                        'tipo_key' => 'aluno',
                        'id' => data_get($aluno, 'bi'),
                        'nome' => data_get($aluno, 'nome'),
                        'dados' => (array) $aluno,
                    ];
                });

            $resultados = $resultados->concat($alunos);
        }

        if (Schema::hasTable('professor')) {
            $professores = DB::table('professor')
                ->where('nome', 'like', "%{$q}%")
                ->orderBy('nome')
                ->get()
                ->map(function ($professor) {
                    return [
                        'tipo' => 'Professor',
                        'tipo_key' => 'professor',
                        'id' => (string) data_get($professor, 'id'),
                        'nome' => data_get($professor, 'nome'),
                        'dados' => (array) $professor,
                    ];
                });

            $resultados = $resultados->concat($professores);
        }

        if (Schema::hasTable('funcionario')) {
            $funcionarios = DB::table('funcionario')
                ->where('nome', 'like', "%{$q}%")
                ->orderBy('nome')
                ->get()
                ->map(function ($funcionario) {
                    return [
                        'tipo' => 'Funcionario',
                        'tipo_key' => 'funcionario',
                        'id' => data_get($funcionario, 'bi_passaporte'),
                        'nome' => data_get($funcionario, 'nome'),
                        'dados' => (array) $funcionario,
                    ];
                });

            $resultados = $resultados->concat($funcionarios);
        }
    }

    return view('database', compact('q', 'resultados', 'lista', 'listaRegistros', 'listaTitulo'));
})->name('database');

Route::get('/database/{tipo}/{id}/editar', function (string $tipo, string $id) {
    $tipo = strtolower($tipo);

    if ($tipo === 'aluno') {
        abort_if(!Schema::hasTable('aluno'), 404);
        $registro = DB::table('aluno')->where('bi', $id)->first();
    } elseif ($tipo === 'professor') {
        abort_if(!Schema::hasTable('professor'), 404);
        $registro = DB::table('professor')->where('id', (int) $id)->first();
    } elseif ($tipo === 'funcionario') {
        abort_if(!Schema::hasTable('funcionario'), 404);
        $registro = DB::table('funcionario')->where('bi_passaporte', $id)->first();
    } else {
        abort(404);
    }

    abort_if(!$registro, 404);

    return view('database_edit', compact('tipo', 'registro', 'id'));
})->name('database.edit');

Route::put('/database/{tipo}/{id}', function (\Illuminate\Http\Request $request, string $tipo, string $id) {
    $tipo = strtolower($tipo);

    if ($tipo === 'aluno') {
        abort_if(!Schema::hasTable('aluno'), 404);
        $registro = DB::table('aluno')->where('bi', $id)->first();
        abort_if(!$registro, 404);

        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:60'],
            'data_nascimento' => ['required', 'date'],
            'sexo' => ['required', 'in:Masculino,Feminino'],
            'bi' => ['required', 'string', 'max:21', Rule::unique('aluno', 'bi')->ignore($id, 'bi')],
            'nacionalidade' => ['required', 'string', 'max:20'],
            'encarregados' => ['required', 'string', 'max:40'],
            'turma' => ['nullable', 'string', 'max:80'],
            'desempenho' => ['nullable', 'numeric', 'between:0,20'],
            'descricao' => ['nullable', 'string', 'max:255'],
        ]);

        $payload = [
            'nome' => trim((string) $dados['nome']),
            'data_nascimento' => $dados['data_nascimento'],
            'sexo' => $dados['sexo'],
            'bi' => trim((string) $dados['bi']),
            'nacionalidade' => trim((string) $dados['nacionalidade']),
            'encarregados' => trim((string) $dados['encarregados']),
        ];

        if (Schema::hasColumn('aluno', 'turma')) {
            $payload['turma'] = isset($dados['turma']) && trim((string) $dados['turma']) !== '' ? trim((string) $dados['turma']) : null;
        }
        if (Schema::hasColumn('aluno', 'desempenho')) {
            $payload['desempenho'] = isset($dados['desempenho']) && $dados['desempenho'] !== '' ? $dados['desempenho'] : null;
        }
        if (Schema::hasColumn('aluno', 'descricao')) {
            $payload['descricao'] = isset($dados['descricao']) && trim((string) $dados['descricao']) !== '' ? trim((string) $dados['descricao']) : null;
        }

        DB::table('aluno')->where('bi', $id)->update($payload);
    } elseif ($tipo === 'professor') {
        abort_if(!Schema::hasTable('professor'), 404);
        $registro = DB::table('professor')->where('id', (int) $id)->first();
        abort_if(!$registro, 404);

        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('professor', 'email')->ignore((int) $id)],
            'telefone' => ['required', 'string', 'max:50'],
            'disciplina' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $update = [
            'nome' => $dados['nome'],
            'email' => $dados['email'],
            'telefone' => $dados['telefone'],
            'disciplina' => $dados['disciplina'],
            'updated_at' => now(),
        ];

        if (!empty($dados['password'])) {
            $update['password'] = Hash::make($dados['password']);
        }

        DB::table('professor')->where('id', (int) $id)->update($update);
    } elseif ($tipo === 'funcionario') {
        abort_if(!Schema::hasTable('funcionario'), 404);
        $registro = DB::table('funcionario')->where('bi_passaporte', $id)->first();
        abort_if(!$registro, 404);

        $regras = [
            'nome' => ['required', 'string', 'max:60'],
            'data_nascimento' => ['required', 'date'],
            'sexo' => ['required', 'in:Masculino,Femenino'],
            'nacionalidade' => ['required', 'string', 'max:20'],
            'bi_passaporte' => ['required', 'string', 'max:21', Rule::unique('funcionario', 'bi_passaporte')->ignore($id, 'bi_passaporte')],
            'contacto' => ['required', 'string', 'max:30'],
            'email' => ['required', 'email', 'max:35'],
            'formacao' => ['required', 'string', 'max:90'],
            'nivel_academico' => ['required', 'string', 'max:25'],
            'endereco' => ['required', 'string', 'max:50'],
            'funcao' => ['required', 'string', 'max:50'],
            'departamento' => ['required', 'string', 'max:40'],
        ];

        if (Schema::hasColumn('funcionario', 'email')) {
            $regras['email'][] = Rule::unique('funcionario', 'email')->ignore($id, 'bi_passaporte');
        }

        $dados = $request->validate($regras);

        try {
            $payload = $dados;

            if (Schema::hasColumn('funcionario', 'updated_at')) {
                $payload['updated_at'] = now();
            }

            DB::table('funcionario')->where('bi_passaporte', $id)->update($payload);
        } catch (\Throwable $e) {
            report($e);
            return redirect()
                ->route('database.edit', ['tipo' => 'funcionario', 'id' => $id, 'q' => $request->query('q', '')])
                ->withInput()
                ->withErrors(['funcionario' => 'Falha ao guardar alteracoes do funcionario.']);
        }
    } else {
        abort(404);
    }

    return redirect()->route('database', ['q' => $request->query('q', '')])->with('success', 'Registro atualizado com sucesso.');
})->name('database.update');

Route::delete('/database/{tipo}/{id}', function (string $tipo, string $id) {
    $tipo = strtolower($tipo);

    if ($tipo === 'aluno') {
        abort_if(!Schema::hasTable('aluno'), 404);
        DB::table('aluno')->where('bi', $id)->delete();
    } elseif ($tipo === 'professor') {
        abort_if(!Schema::hasTable('professor'), 404);
        DB::table('professor')->where('id', (int) $id)->delete();
    } elseif ($tipo === 'funcionario') {
        abort_if(!Schema::hasTable('funcionario'), 404);
        DB::table('funcionario')->where('bi_passaporte', $id)->delete();
    } else {
        abort(404);
    }

    return redirect()->route('database')->with('success', 'Registro eliminado com sucesso.');
})->name('database.destroy');

Route::get('/register_funcionario', function() {
    return view('register_funcionario');
})->name('register_funcionario');

Route::get('/register_aluno', function () use ($obterListaTurmas) {
    $turmas = $obterListaTurmas();

    return view('register_aluno', compact('turmas'));
})->name('register_aluno');

Route::get('/register_professor', function () {
    return view('register_professor');
})->name('register_professor');

Route::post('/register_aluno', function (\Illuminate\Http\Request $request) use ($obterTabelaTurmas, $obterPrimeiraColunaExistente) {
    $tabelaTurmas = $obterTabelaTurmas();
    if (!$tabelaTurmas) {
        return redirect()->route('register_aluno')
            ->withInput()
            ->with('error', 'Cadastro nao foi efetuado. A tabela de turma nao existe na base de dados ativa.');
    }

    $colunaIdTurma = $obterPrimeiraColunaExistente($tabelaTurmas, ['id', 'turma_id']);
    $colunaNomeTurma = $obterPrimeiraColunaExistente($tabelaTurmas, ['nome_turma', 'nome', 'turma']);
    if (!$colunaIdTurma || !$colunaNomeTurma) {
        return redirect()->route('register_aluno')
            ->withInput()
            ->with('error', 'Cadastro nao foi efetuado. A tabela de turma nao possui colunas validas para id/nome.');
    }

    $dados = $request->validate([
        'nome' => ['required', 'string', 'max:60'],
        'data_nascimento' => ['required', 'date'],
        'sexo' => ['required', 'in:Masculino,Feminino'],
        'bi' => ['required', 'string', 'max:21'],
        'nacionalidade' => ['required', 'string', 'max:20'],
        'encarregados' => ['required', 'string', 'max:40'],
        'turma' => ['required', 'string', 'max:30'],
        'id_turma' => ['required', 'integer', 'min:1', Rule::exists($tabelaTurmas, $colunaIdTurma)],
        'contactoencarregado' => ['required', 'integer', 'between:0,999999999'],
        'contactoalternativo' => ['required', 'integer', 'between:0,999999999'],
    ]);

    if (!Schema::hasTable('aluno')) {
        return redirect()->route('register_aluno')
            ->withInput()
            ->with('error', 'Cadastro nao foi efetuado. A tabela aluno nao existe na base de dados ativa.');
    }

    $colunaDocumentoAluno = $obterPrimeiraColunaExistente('aluno', ['bi', 'bi_passaporte', 'documento', 'numero_documento']);
    if (!$colunaDocumentoAluno) {
        return redirect()->route('register_aluno')
            ->withInput()
            ->with('error', 'Cadastro nao foi efetuado. A tabela aluno nao possui coluna para BI/documento.');
    }

    $documentoNormalizado = trim((string) $dados['bi']);
    $documentoDuplicado = DB::table('aluno')->where($colunaDocumentoAluno, $documentoNormalizado)->exists();

    if ($documentoDuplicado) {
        return redirect()->route('register_aluno')
            ->withInput()
            ->withErrors(['bi' => 'Este BI/documento ja esta registado.']);
    }

    try {
        $turmaRelacionada = DB::table($tabelaTurmas)
            ->where($colunaIdTurma, (int) $dados['id_turma'])
            ->first();

        if (!$turmaRelacionada) {
            return redirect()->route('register_aluno')
                ->withInput()
                ->with('error', 'Cadastro nao foi efetuado. A turma selecionada nao foi encontrada.');
        }

        $payload = [
            'nome' => trim($dados['nome']),
            'data_nascimento' => $dados['data_nascimento'],
            'sexo' => $dados['sexo'],
            'nacionalidade' => trim($dados['nacionalidade']),
            'encarregados' => trim($dados['encarregados']),
            'contactoencarregado' => $dados['contactoencarregado'],
            'contactoalternativo' => $dados['contactoalternativo'],
        ];

        $payload[$colunaDocumentoAluno] = $documentoNormalizado;

        if (Schema::hasColumn('aluno', 'turma')) {
            $payload['turma'] = (string) data_get($turmaRelacionada, $colunaNomeTurma);
        }

        if (Schema::hasColumn('aluno', 'id_turma')) {
            $payload['id_turma'] = $dados['id_turma'];
        }

        if (Schema::hasColumn('aluno', 'turma_id')) {
            $payload['turma_id'] = $dados['id_turma'];
        }

        foreach (['contactoencarregado', 'contactoalternativo', 'nacionalidade', 'encarregados', 'data_nascimento', 'sexo', 'nome'] as $coluna) {
            if (!Schema::hasColumn('aluno', $coluna)) {
                unset($payload[$coluna]);
            }
        }

        DB::table('aluno')->insert($payload);
    } catch (\Throwable $e) {
        report($e);
        return redirect()->route('register_aluno')
            ->withInput()
            ->with('error', 'Cadastro nao foi efetuado. Falhou ao guardar no banco de dados: ' . $e->getMessage());
    }

    return redirect()->route('register_aluno')->with('success', 'Aluno cadastrado com sucesso.');
})->name('register_aluno.store');

Route::post('/register_professor', function (\Illuminate\Http\Request $request) {
    if (!Schema::hasTable('professor')) {
        return redirect()->route('register_professor')
            ->withInput()
            ->with('error', 'Cadastro nao foi efetuado. A tabela professor nao existe na base de dados ativa.');
    }

    $regras = [
        'nome' => ['required', 'string', 'max:60'],
        'data_nascimento' => ['required', 'date'],
        'sexo' => ['required', 'in:Masculino,Femenino'],
        'nacionalidade' => ['required', 'string', 'max:20'],
        'bi_passaporte' => ['required', 'string', 'max:21'],
        'contacto' => ['required', 'string', 'max:30'],
        'email' => ['required', 'email', 'max:255'],
        'formacao' => ['required', 'string', 'max:90'],
        'nivel_academico' => ['required', 'string', 'max:25'],
        'endereco' => ['required', 'string', 'max:50'],
        'turma' => ['required', 'string', 'max:30'],
    ];

    if (Schema::hasColumn('professor', 'bi_passaporte')) {
        $regras['bi_passaporte'][] = Rule::unique('professor', 'bi_passaporte');
    }
    if (Schema::hasColumn('professor', 'email')) {
        $regras['email'][] = Rule::unique('professor', 'email');
    }

    $dados = $request->validate($regras);

    try {
        $payload = [];

        if (Schema::hasColumn('professor', 'nome')) {
            $payload['nome'] = trim((string) $dados['nome']);
        }
        if (Schema::hasColumn('professor', 'data_nascimento')) {
            $payload['data_nascimento'] = $dados['data_nascimento'];
        }
        if (Schema::hasColumn('professor', 'sexo')) {
            $payload['sexo'] = $dados['sexo'];
        }
        if (Schema::hasColumn('professor', 'nacionalidade')) {
            $payload['nacionalidade'] = trim((string) $dados['nacionalidade']);
        }
        if (Schema::hasColumn('professor', 'bi_passaporte')) {
            $payload['bi_passaporte'] = trim((string) $dados['bi_passaporte']);
        }
        if (Schema::hasColumn('professor', 'contacto')) {
            $payload['contacto'] = trim((string) $dados['contacto']);
        }
        if (Schema::hasColumn('professor', 'telefone')) {
            $payload['telefone'] = trim((string) $dados['contacto']);
        }
        if (Schema::hasColumn('professor', 'email')) {
            $payload['email'] = strtolower(trim((string) $dados['email']));
        }
        if (Schema::hasColumn('professor', 'formacao')) {
            $payload['formacao'] = trim((string) $dados['formacao']);
        }
        if (Schema::hasColumn('professor', 'nivel_academico')) {
            $payload['nivel_academico'] = trim((string) $dados['nivel_academico']);
        }
        if (Schema::hasColumn('professor', 'endereco')) {
            $payload['endereco'] = trim((string) $dados['endereco']);
        }
        if (Schema::hasColumn('professor', 'turma')) {
            $payload['turma'] = trim((string) $dados['turma']);
        }
        if (Schema::hasColumn('professor', 'disciplina')) {
            $payload['disciplina'] = trim((string) $dados['turma']);
        }
        if (Schema::hasColumn('professor', 'password')) {
            $payload['password'] = Hash::make((string) $dados['bi_passaporte']);
        }
        if (Schema::hasColumn('professor', 'created_at')) {
            $payload['created_at'] = now();
        }
        if (Schema::hasColumn('professor', 'updated_at')) {
            $payload['updated_at'] = now();
        }

        DB::table('professor')->insert($payload);
    } catch (\Throwable $e) {
        report($e);
        return redirect()->route('register_professor')
            ->withInput()
            ->with('error', 'Cadastro nao foi efetuado. Falhou ao guardar no banco de dados: ' . $e->getMessage());
    }

    return redirect()->route('register_professor')->with('success', 'Professor cadastrado com sucesso.');
})->name('register_professor.store');

Route::post('/register_funcionario', function (\Illuminate\Http\Request $request) {
    if (!Schema::hasTable('funcionario')) {
        return redirect()->route('register_funcionario')
            ->withInput()
            ->with('error', 'Cadastro nao foi efetuado. A tabela funcionario nao existe na base de dados ativa.');
    }

    $regras = [
        'nome' => ['required', 'string', 'max:60'],
        'data_nascimento' => ['required', 'date'],
        'sexo' => ['required', 'in:Masculino,Femenino'],
        'nacionalidade' => ['required', 'string', 'max:20'],
        'bi_passaporte' => ['required', 'string', 'max:21'],
        'contacto' => ['required', 'string', 'max:30'],
        'email' => ['required', 'email', 'max:35'],
        'formacao' => ['required', 'string', 'max:90'],
        'nivel_academico' => ['required', 'string', 'max:25'],
        'endereco' => ['required', 'string', 'max:50'],
        'funcao' => ['required', 'string', 'max:50'],
        'departamento' => ['required', 'string', 'max:40'],
    ];

    if (Schema::hasColumn('funcionario', 'bi_passaporte')) {
        $regras['bi_passaporte'][] = Rule::unique('funcionario', 'bi_passaporte');
    }
    if (Schema::hasColumn('funcionario', 'email')) {
        $regras['email'][] = Rule::unique('funcionario', 'email');
    }

    $dados = $request->validate($regras);

    try {
        $payload = [];

        if (Schema::hasColumn('funcionario', 'nome')) {
            $payload['nome'] = trim((string) $dados['nome']);
        }
        if (Schema::hasColumn('funcionario', 'data_nascimento')) {
            $payload['data_nascimento'] = $dados['data_nascimento'];
        }
        if (Schema::hasColumn('funcionario', 'sexo')) {
            $payload['sexo'] = $dados['sexo'];
        }
        if (Schema::hasColumn('funcionario', 'nacionalidade')) {
            $payload['nacionalidade'] = trim((string) $dados['nacionalidade']);
        }
        if (Schema::hasColumn('funcionario', 'bi_passaporte')) {
            $payload['bi_passaporte'] = trim((string) $dados['bi_passaporte']);
        }
        if (Schema::hasColumn('funcionario', 'contacto')) {
            $payload['contacto'] = trim((string) $dados['contacto']);
        }
        if (Schema::hasColumn('funcionario', 'telefone')) {
            $payload['telefone'] = trim((string) $dados['contacto']);
        }
        if (Schema::hasColumn('funcionario', 'email')) {
            $payload['email'] = strtolower(trim((string) $dados['email']));
        }
        if (Schema::hasColumn('funcionario', 'formacao')) {
            $payload['formacao'] = trim((string) $dados['formacao']);
        }
        if (Schema::hasColumn('funcionario', 'nivel_academico')) {
            $payload['nivel_academico'] = trim((string) $dados['nivel_academico']);
        }
        if (Schema::hasColumn('funcionario', 'endereco')) {
            $payload['endereco'] = trim((string) $dados['endereco']);
        }
        if (Schema::hasColumn('funcionario', 'funcao')) {
            $payload['funcao'] = trim((string) $dados['funcao']);
        }
        if (Schema::hasColumn('funcionario', 'departamento')) {
            $payload['departamento'] = trim((string) $dados['departamento']);
        }
        if (Schema::hasColumn('funcionario', 'password')) {
            $payload['password'] = Hash::make((string) $dados['bi_passaporte']);
        }
        if (Schema::hasColumn('funcionario', 'created_at')) {
            $payload['created_at'] = now();
        }
        if (Schema::hasColumn('funcionario', 'updated_at')) {
            $payload['updated_at'] = now();
        }

        DB::table('funcionario')->insert($payload);
    } catch (\Throwable $e) {
        report($e);
        return redirect()->route('register_funcionario')
            ->withInput()
            ->with('error', 'Cadastro nao foi efetuado. Falhou ao guardar no banco de dados: ' . $e->getMessage());
    }

    return redirect()->route('register_funcionario')->with('success', 'Funcionario cadastrado com sucesso.');
})->name('register_funcionario.store');
