<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela do Professor</title>
    <style>
        body { margin: 0; font-family: "Segoe UI", sans-serif; background: #f3f7ff; }
        header { background: linear-gradient(45deg, #002168, #3676ff); color: #fff; padding: 18px 24px; display: flex; gap: 10px; margin-bottom: 18px; flex-wrap: wrap; }
        main { padding: 24px; }
        .teacher-nav { display: flex; gap: 10px; margin-bottom: 18px; flex-wrap: wrap; }
        .teacher-link { text-decoration: none; padding: 10px 14px; border-radius: 8px; background: #e7eefc; color: #0b2447; font-weight: 600; }
        .teacher-link.active { background: #0b2447; color: #fff; }
        .card { background: #fff; border-radius: 12px; padding: 22px; box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08); max-width: 900px; }
        .notice-list { margin-top: 14px; display: grid; gap: 10px; }
        .notice-item { border: 1px solid #dbe6fb; border-radius: 10px; padding: 12px; background: #fdfefe; }
        .notice-meta { color: #475569; font-size: 13px; margin-top: 4px; }
        .notice-body { margin-top: 8px; white-space: pre-wrap; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { text-align: left; border-bottom: 1px solid #e5e7eb; padding: 8px; }
        .form-card { margin-top: 14px; border: 1px solid #dbe6fb; border-radius: 10px; padding: 12px; background: #f8fbff; }
        .input-group { margin-bottom: 10px; }
        .input-group label { display: block; font-weight: 600; margin-bottom: 6px; color: #0b2447; }
        .input-group input, .input-group select { width: 100%; padding: 10px 12px; border-radius: 8px; border: 1px solid #c9d6f2; background: #fff; }
        .btn-primary { border: none; border-radius: 8px; background: #0b2447; color: #fff; padding: 10px 14px; font-weight: 600; cursor: pointer; }
        .alert { margin-top: 10px; padding: 10px 12px; border-radius: 8px; }
        .alert.success { background: #dcfce7; color: #166534; border: 1px solid #bbf7d0; }
        .alert.error { background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
        .turma-meta {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(170px, 1fr));
            gap: 8px;
            margin-top: 10px;
        }
        .turma-chip {
            background: #f8fbff;
            border: 1px solid #dbe6fb;
            border-radius: 10px;
            padding: 8px 10px;
        }
        .turma-chip small {
            display: block;
            font-size: 11px;
            color: #64748b;
            margin-bottom: 2px;
            text-transform: uppercase;
            letter-spacing: .02em;
        }
        .turma-chip strong {
            display: block;
            color: #0b2447;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    <header>
        <a href="{{ route('telaprofessor') }}"><img src="{{ asset('img/jardim-logotipo122.png') }}" alt="Imagem" width="120px" height="70px" class="logo"></a>
        <h3>Ambiente do Professor</h3>
    </header>
    <main>
        @php($aba = $aba ?? 'turma')
        <nav class="teacher-nav">
            <a class="teacher-link {{ $aba === 'turma' ? 'active' : '' }}" href="{{ route('telaprofessor.turma') }}">Turma</a>
            <a class="teacher-link {{ $aba === 'notificacoes' ? 'active' : '' }}" href="{{ route('telaprofessor.notificacoes') }}">Notificacoes</a>
            <a class="teacher-link {{ $aba === 'chat' ? 'active' : '' }}" href="{{ route('telaprofessor.chat') }}">Chat</a>
            <a class="teacher-link {{ $aba === 'perfil' ? 'active' : '' }}" href="{{ route('telaprofessor.perfil') }}">Perfil</a>
            <a href="{{ route('logout') }}" class="teacher-link">
                <i class="fa-solid fa-right-from-bracket"></i> Sair
            </a>
        </nav>

        <section class="card">
            @if (session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert error">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert error">{{ $errors->first() }}</div>
            @endif

            @if ($aba === 'turma')
                <h4>Turma</h4>
                <p>Professor: <strong>{{ $nomeProfessor ?? '-' }}</strong></p>

                @if (!empty($turma))
                    <div class="turma-meta">
                        <div class="turma-chip">
                            <small>Turma</small>
                            <strong>{{ data_get($turma, 'nome_turma', '-') }}</strong>
                        </div>
                        <div class="turma-chip">
                            <small>Idade</small>
                            <strong>{{ data_get($turma, 'idade_alunos', '-') }}</strong>
                        </div>
                        <div class="turma-chip">
                            <small>Professor titular</small>
                            <strong>{{ data_get($turma, 'professor', '-') }}</strong>
                        </div>
                        <div class="turma-chip">
                            <small>Professor auxiliar</small>
                            <strong>{{ data_get($turma, 'professor_auxiliar', '-') }}</strong>
                        </div>
                        <div class="turma-chip">
                            <small>Tempo diario</small>
                            <strong>{{ data_get($turma, 'tempo_aula_diaria', '-') }}</strong>
                        </div>
                    </div>

                    <h4 style="margin-top:16px;">Lista de alunos da turma</h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Aluno</th>
                                <th>Referencia</th>
                                <th>Desempenho</th>
                                <th>Descricao</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse (($alunosTurma ?? collect()) as $aluno)
                                <tr>
                                    <td>{{ data_get($aluno, 'nome', '-') }}</td>
                                    <td>{{ data_get($aluno, $colunaReferenciaAluno ?? 'id', '-') }}</td>
                                    <td>{{ !empty($colunaDesempenho) ? data_get($aluno, $colunaDesempenho, '-') : '-' }}</td>
                                    <td>{{ !empty($colunaDescricao) ? data_get($aluno, $colunaDescricao, '-') : '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4">Sem alunos associados a esta turma.</td></tr>
                            @endforelse
                        </tbody>
                    </table>

                    <form method="POST" action="{{ route('academia.turmas.alunos.notas', data_get($turma, 'id')) }}" class="form-card">
                        @csrf
                        <div class="input-group">
                            <label>Selecionar aluno</label>
                            <select name="aluno_ref" required @if (empty($colunaReferenciaAluno) || ($alunosTurma ?? collect())->isEmpty()) disabled @endif>
                                <option value="">Selecionar aluno</option>
                                @foreach (($alunosTurma ?? collect()) as $aluno)
                                    <option value="{{ data_get($aluno, $colunaReferenciaAluno) }}">
                                        {{ data_get($aluno, 'nome', '-') }} ({{ data_get($aluno, $colunaReferenciaAluno, '-') }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="input-group">
                            <label>Desempenho (0 a 20)</label>
                            <input type="number" name="desempenho" min="0" max="20" step="0.1" required>
                        </div>
                        <div class="input-group">
                            <label>Descricao</label>
                            <input type="text" name="descricao" maxlength="255" placeholder="Observacao sobre o desempenho">
                        </div>
                        <button type="submit" class="btn-primary" @if (empty($colunaReferenciaAluno) || empty($colunaDesempenho)) disabled @endif>Guardar desempenho</button>
                        @if (empty($colunaDesempenho))
                            <p style="margin-top:8px;">A tabela aluno nao possui coluna de desempenho.</p>
                        @endif
                        @if (empty($colunaDescricao))
                            <p style="margin-top:6px;">A tabela aluno nao possui coluna de descricao compativel.</p>
                        @endif
                    </form>
                @else
                    <p>Nenhuma turma associada a este professor.</p>
                @endif
            @elseif ($aba === 'notificacoes')
                <h4>Notificacoes</h4>
                <div class="notice-list">
                    @forelse (($noticias ?? collect()) as $noticia)
                        <article class="notice-item">
                            <strong>{{ data_get($noticia, 'assunto', '-') }}</strong>
                            <p class="notice-meta">
                                {{ data_get($noticia, 'data_publicacao', '-') }} as {{ data_get($noticia, 'hora_publicacao', '-') }}
                            </p>
                            <p class="notice-body">{{ data_get($noticia, 'nota', '-') }}</p>
                        </article>
                    @empty
                        <p>Sem notificacoes no momento.</p>
                    @endforelse
                </div>
            @elseif ($aba === 'chat')
                <h4>Chat</h4>
                <p>Area de chat do professor pronta para conteudo.</p>
            @else
                <h4>Perfil</h4>
                @if (!empty($professorPerfil))
                    <p>Dados completos do professor logado.</p>
                    <table class="table">
                        <tbody>
                            @foreach ((array) $professorPerfil as $campo => $valor)
                                @continue($campo === 'password')
                                <tr>
                                    <th style="text-transform:capitalize;">{{ str_replace('_', ' ', $campo) }}</th>
                                    <td>{{ $valor !== null && $valor !== '' ? $valor : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>Nao foi possivel localizar os dados do professor logado na tabela professor.</p>
                @endif
            @endif
        </section>
    </main>
</body>
</html>


