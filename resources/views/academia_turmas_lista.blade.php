<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Academia - Lista da Turma</title>

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .page-grid {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 16px;
            margin-top: 16px;
        }
        .mini-title {
            margin-bottom: 10px;
            text-align: left;
            color: #0b2447;
        }
        .table td, .table th {
            text-align: left;
        }
        .form-card {
            background: #f8fbff;
            border: 1px solid #d6e3ff;
            border-radius: 14px;
            padding: 14px;
            margin-top: 10px;
        }
        .input-group {
            margin-bottom: 10px;
        }
        .input-group label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #0b2447;
            text-align: left;
        }
        .input-group select,
        .input-group input {
            width: 100%;
            padding: 9px 10px;
            border: 1px solid #c9d6f2;
            border-radius: 8px;
            background: #fff;
        }
        .form-actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            justify-content: flex-start;
        }
        .alert {
            margin-top: 12px;
            padding: 10px 12px;
            border-radius: 8px;
            text-align: left;
        }
        .alert.success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #bbf7d0;
        }
        .alert.error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fecaca;
        }
        .is-hidden {
            display: none;
        }
        .search-results {
            margin-top: 8px;
            border: 1px solid #d6e3ff;
            border-radius: 8px;
            background: #fff;
            max-height: 220px;
            overflow-y: auto;
        }
        .search-result-item {
            width: 100%;
            border: none;
            background: #fff;
            text-align: left;
            padding: 8px 10px;
            cursor: pointer;
            border-bottom: 1px solid #eef3ff;
        }
        .search-result-item:last-child {
            border-bottom: none;
        }
        .search-result-item:hover {
            background: #f3f7ff;
        }
        .search-empty {
            padding: 10px;
            color: #475569;
            text-align: left;
        }
        @media (max-width: 900px) {
            .page-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}"><img src="{{ asset('img/jardim-logotipo122.png') }}" alt="Imagem" width="150px" height="100px" class="logo"></a>
        <ul>
            <li><a href="{{ route('dashboard') }}"><i class="fa fa-chart-pie"></i> Dashboard</a></li>
            <li><a href="{{ route('register') }}"><i class="fa fa-id-card"></i> Cadastro</a></li>
            <li><a href="{{ route('professores') }}"><i class="fa fa-chalkboard-teacher"></i> Professores</a></li>
            <li><a href="{{ route('alunos') }}"><i class="fa fa-user-graduate"></i> Alunos</a></li>
            <li class="active"><a href="{{ route('academia') }}"><i class="fa fa-calendar-days"></i> Academia</a></li>
            <li><a href="{{ route('database') }}"><i class="fa fa-database"></i> Database</a></li>
            <li><a href="{{ route('perfil') }}"><i class="fa fa-user"></i> Perfil</a></li>
            <li><a href="{{ route('logout') }}" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Sair</a></li>
        </ul>
    </aside>

    <main class="content">
        <section class="card">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap;">
                <h4 style="text-align:left;">Turma: {{ data_get($turma, 'nome_turma', '-') }}</h4>
                <a href="{{ route('academia.turmas') }}" class="btn">Voltar</a>
            </div>

            @if (session('success'))
                <div class="alert success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="alert error">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
                <div class="alert error">{{ $errors->first() }}</div>
            @endif

            <div class="page-grid">
                <div>
                    <h4 class="mini-title">Caracteristicas da turma</h4>
                    <table class="table">
                        <tbody>
                            <tr><th>Nome</th><td>{{ data_get($turma, 'nome_turma', '-') }}</td></tr>
                            <tr><th>Idade dos alunos</th><td>{{ data_get($turma, 'idade_alunos', '-') }}</td></tr>
                            <tr><th>Professor</th><td>{{ data_get($turma, 'professor', '-') }}</td></tr>
                            <tr><th>Professor auxiliar</th><td>{{ data_get($turma, 'professor_auxiliar', '-') }}</td></tr>
                            <tr><th>Tempo diario</th><td>{{ data_get($turma, 'tempo_aula_diaria', '-') }}</td></tr>
                        </tbody>
                    </table>

                    <h4 class="mini-title" style="margin-top:16px;">Lista de alunos</h4>
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
                            @forelse ($alunosTurma as $aluno)
                                <tr>
                                    <td>{{ data_get($aluno, 'nome', '-') }}</td>
                                    <td>{{ data_get($aluno, $colunaReferenciaAluno ?? 'id', '-') }}</td>
                                    <td>{{ !empty($colunaDesempenho) ? data_get($aluno, $colunaDesempenho, '-') : '-' }}</td>
                                    <td>{{ !empty($colunaDescricao) ? data_get($aluno, $colunaDescricao, '-') : '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">Sem alunos associados a esta turma.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div>
                    <h4 class="mini-title">Gerir alunos da turma</h4>

                    <form method="POST" action="{{ route('academia.turmas.destroy', data_get($turma, 'id')) }}" class="form-card" onsubmit="return confirm('Deseja eliminar esta turma? Esta acao remove a turma e desvincula os alunos associados.');">
                        @csrf
                        @method('DELETE')
                        <div class="input-group" style="margin-bottom:0;">
                            <label>Eliminar turma</label>
                            <p style="margin:0; text-align:left; color:#475569;">Apaga a turma selecionada e remove os vinculos dos alunos com esta turma.</p>
                        </div>
                        <div class="form-actions" style="margin-top:10px;">
                            <button type="submit" class="btn delete">Deletar turma</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('academia.turmas.alunos.adicionar', data_get($turma, 'id')) }}" class="form-card">
                        @csrf
                        <div class="form-actions">
                            <button type="button" id="toggle-add-aluno" class="btn edit">Adicionar aluno</button>
                        </div>
                        <div id="add-aluno-panel" class="input-group is-hidden" style="margin-top:10px;">
                            <label>Pesquisar aluno por nome</label>
                            <input type="text" id="search-aluno-input" placeholder="Digite o nome do aluno">
                            <input type="hidden" id="search-aluno-ref" name="aluno_ref" required>
                            <div id="search-aluno-results" class="search-results is-hidden"></div>
                            <p id="search-aluno-selected" style="margin-top:8px; text-align:left; color:#0b2447; font-weight:600;"></p>
                            @if (empty($colunaLigacao) || empty($colunaReferenciaAluno))
                                <p style="margin-top:8px; text-align:left;">A pesquisa funciona, mas a associacao automatica a turma depende de coluna `turma_id` ou `turma` na tabela aluno.</p>
                            @endif
                        </div>
                        <div class="form-actions is-hidden" id="add-aluno-submit-wrap" style="margin-top:10px;">
                            <button type="submit" class="btn edit" @if (empty($colunaLigacao) || empty($colunaReferenciaAluno)) disabled @endif>Confirmar adicionar</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('academia.turmas.alunos.remover', data_get($turma, 'id')) }}" class="form-card">
                        @csrf
                        <div class="input-group">
                            <label>Remover aluno</label>
                            @if (empty($colunaReferenciaAluno))
                                <p>Estrutura atual da tabela aluno nao permite identificar alunos para remocao.</p>
                            @else
                                <select name="aluno_ref" required>
                                    <option value="">Selecionar aluno da turma</option>
                                    @foreach ($alunosTurma as $aluno)
                                        <option value="{{ data_get($aluno, $colunaReferenciaAluno) }}">
                                            {{ data_get($aluno, 'nome', '-') }} ({{ data_get($aluno, $colunaReferenciaAluno, '-') }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn delete" @if (empty($colunaReferenciaAluno)) disabled @endif>Remover aluno</button>
                        </div>
                    </form>

                    <form method="POST" action="{{ route('academia.turmas.alunos.notas', data_get($turma, 'id')) }}" class="form-card">
                        @csrf
                        <div class="input-group">
                            <label>Editar desempenho</label>
                            @if (empty($colunaReferenciaAluno))
                                <p>Estrutura atual da tabela aluno nao permite identificar alunos para desempenho.</p>
                            @else
                                <select name="aluno_ref" required>
                                    <option value="">Selecionar aluno da turma</option>
                                    @foreach ($alunosTurma as $aluno)
                                        <option value="{{ data_get($aluno, $colunaReferenciaAluno) }}">
                                            {{ data_get($aluno, 'nome', '-') }} ({{ data_get($aluno, $colunaReferenciaAluno, '-') }})
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                        <div class="input-group">
                            <label>Desempenho (0 a 20)</label>
                            <input type="number" name="desempenho" min="0" max="20" step="0.1" required>
                        </div>
                        <div class="input-group">
                            <label>Descricao</label>
                            <input type="text" name="descricao" maxlength="255" placeholder="Observacao sobre o desempenho">
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn edit" @if (empty($colunaReferenciaAluno) || empty($colunaDesempenho)) disabled @endif>Guardar desempenho</button>
                        </div>
                        @if (empty($colunaDesempenho))
                            <p style="margin-top:8px; text-align:left;">A tabela aluno ainda nao possui coluna de desempenho (`desempenho`, `nota`, `nota_final` ou `media`).</p>
                        @endif
                        @if (empty($colunaDescricao))
                            <p style="margin-top:8px; text-align:left;">A tabela aluno ainda nao possui coluna de descricao (`descricao`, `descricao_desempenho` ou `observacao`).</p>
                        @endif
                    </form>
                </div>
            </div>
        </section>
    </main>
    <script>
        (function () {
            const toggleBtn = document.getElementById('toggle-add-aluno');
            const panel = document.getElementById('add-aluno-panel');
            const input = document.getElementById('search-aluno-input');
            const hiddenRef = document.getElementById('search-aluno-ref');
            const results = document.getElementById('search-aluno-results');
            const selectedInfo = document.getElementById('search-aluno-selected');
            const submitWrap = document.getElementById('add-aluno-submit-wrap');

            if (!toggleBtn || !panel || !submitWrap) {
                return;
            }

            const hasSearch = !!(input && hiddenRef && results && selectedInfo);

            const turmaId = @json((int) data_get($turma, 'id'));
            const endpointBase = @json(route('academia.turmas.alunos.pesquisar', data_get($turma, 'id')));
            let debounceTimer = null;

            const limparSelecao = () => {
                if (!hiddenRef || !selectedInfo) {
                    return;
                }
                hiddenRef.value = '';
                selectedInfo.textContent = '';
                submitWrap.classList.add('is-hidden');
            };

            const renderResultados = (alunos) => {
                if (!Array.isArray(alunos) || alunos.length === 0) {
                    results.innerHTML = '<div class="search-empty">Nenhum aluno encontrado.</div>';
                    results.classList.remove('is-hidden');
                    return;
                }

                results.innerHTML = alunos.map((aluno) => {
                    const nome = (aluno.nome || '').toString();
                    const referencia = (aluno.referencia || '').toString();
                    return '<button type="button" class="search-result-item" data-ref="' + referencia.replace(/"/g, '&quot;') + '" data-nome="' + nome.replace(/"/g, '&quot;') + '">' + nome + ' (' + referencia + ')</button>';
                }).join('');
                results.classList.remove('is-hidden');
            };

            toggleBtn.addEventListener('click', () => {
                panel.classList.toggle('is-hidden');
                if (!panel.classList.contains('is-hidden')) {
                    if (hasSearch) {
                        input.focus();
                    }
                } else {
                    limparSelecao();
                    if (results) {
                        results.innerHTML = '';
                        results.classList.add('is-hidden');
                    }
                }
            });

            if (!hasSearch) {
                return;
            }

            input.addEventListener('input', () => {
                const termo = input.value.trim();
                limparSelecao();

                if (termo.length < 2) {
                    results.innerHTML = '';
                    results.classList.add('is-hidden');
                    return;
                }

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(async () => {
                    try {
                        const response = await fetch(endpointBase + '?q=' + encodeURIComponent(termo) + '&turma_id=' + encodeURIComponent(String(turmaId)), {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        if (!response.ok) {
                            throw new Error('Falha na pesquisa');
                        }

                        const payload = await response.json();
                        renderResultados(payload.alunos || []);
                    } catch (error) {
                        results.innerHTML = '<div class="search-empty">Erro ao pesquisar alunos.</div>';
                        results.classList.remove('is-hidden');
                    }
                }, 250);
            });

            results.addEventListener('click', (event) => {
                const target = event.target;
                if (!(target instanceof HTMLElement) || !target.classList.contains('search-result-item')) {
                    return;
                }

                const referencia = target.getAttribute('data-ref') || '';
                const nome = target.getAttribute('data-nome') || '';
                hiddenRef.value = referencia;
                selectedInfo.textContent = 'Selecionado: ' + nome + ' (' + referencia + ')';
                submitWrap.classList.remove('is-hidden');
                results.classList.add('is-hidden');
            });
        })();
    </script>
</body>
</html>

