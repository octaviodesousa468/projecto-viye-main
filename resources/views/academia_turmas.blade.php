<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Academia - Turmas</title>

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .is-hidden { display: none; }
        .form-card {
            background: #f8fbff;
            border: 1px solid #d6e3ff;
            border-radius: 14px;
            padding: 16px;
            margin-top: 12px;
            box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.6);
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }
        .form-grid .input-group {
            margin-bottom: 0;
        }
        .input-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #0b2447;
        }
        .input-group input,
        .input-group select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #c9d6f2;
            background: #fff;
            outline: none;
        }
        .input-group input:focus,
        .input-group select:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
        }
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 14px;
        }
        .btn.edit {
            background: #0b2447;
            color: #fff;
        }
        .btn.edit:hover {
            background: #153a7a;
        }
        @media (max-width: 720px) {
            .form-grid { grid-template-columns: 1fr; }
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
            <h4>Pesquisar Turmas</h4>
            <form method="GET" action="{{ route('academia.turmas') }}" style="display:flex; gap:8px; margin-top:12px;">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Pesquisar por turma..." style="padding:8px 10px; border:1px solid #ccc; border-radius:6px; flex:1;">
                <button type="submit" class="btn edit">Pesquisar</button>
            </form>
            @if (!empty($q))
            <div style="margin-top:16px;">
                <h4>Resultados</h4>
                <table class="table" style="margin-top:12px;">
                    <thead>
                        <tr>
                            <th>Turma</th>
                            <th>Idade</th>
                            <th>Professor</th>
                            <th>Professor Auxiliar</th>
                            <th>Tempo Diario</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($turmas as $turma)
                            <tr>
                                <td>{{ data_get($turma, 'nome_turma', '-') }}</td>
                                <td>{{ data_get($turma, 'idade_alunos', '-') }}</td>
                                <td>{{ data_get($turma, 'professor', '-') }}</td>
                                <td>{{ data_get($turma, 'professor_auxiliar', '-') }}</td>
                                <td>{{ data_get($turma, 'tempo_aula', data_get($turma, 'tempo_aula_diaria', data_get($turma, 'tempo_aula_diario', '-'))) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align:center;">Sem resultados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif
        </section>

        <section class="card" style="margin-top:16px;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <h4>Criar Turma</h4>
                <button type="button" class="btn edit" onclick="document.getElementById('criar-turma-form').classList.toggle('is-hidden')">
                    Criar turma
                </button>
            </div>

            <form id="criar-turma-form" class="is-hidden form-card" method="POST" action="{{ route('academia.turmas.store') }}">
                @csrf
                <div class="form-grid">
                    <div class="input-group">
                        <label>Nome da turma</label>
                        <input type="text" name="nome_turma" value="{{ old('nome_turma') }}" placeholder="Ex: Turma A">
                    </div>
                    <div class="input-group">
                        <label>Idade dos alunos</label>
                        <input type="text" name="idade_alunos" value="{{ old('idade_alunos') }}" placeholder="Ex: 10-12">
                    </div>
                    <div class="input-group">
                        <label>Professor</label>
                        <select name="professor" id="professor_nome" required>
                            <option value="">Selecionar professor</option>
                            @foreach (($professoresLookup ?? collect()) as $professorOpcao)
                                @php
                                    $nomeProfessorOpcao = (string) data_get($professorOpcao, 'nome', '');
                                    $idProfessorOpcao = (string) data_get($professorOpcao, 'id', '');
                                @endphp
                                <option value="{{ $nomeProfessorOpcao }}" data-id="{{ $idProfessorOpcao }}" {{ old('professor') === $nomeProfessorOpcao ? 'selected' : '' }}>
                                    {{ $nomeProfessorOpcao }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="input-group">
                        <label>ID do professor</label>
                        <input type="text" name="professor_id" id="professor_id" value="{{ old('professor_id') }}" placeholder="Preenchido automaticamente" readonly>
                    </div>
                    <div class="input-group">
                        <label>Professor auxiliar</label>
                        <input type="text" name="professor_auxiliar" value="{{ old('professor_auxiliar') }}" placeholder="Nome do auxiliar">
                    </div>
                    <div class="input-group">
                        <label>Tempo de aula</label>
                        <input type="time" name="tempo_aula" value="{{ old('tempo_aula') }}">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn edit">Guardar turma</button>
                </div>
            </form>
        </section>

        <section class="card" style="margin-top:16px;">
            <div style="display:flex; align-items:center; justify-content:space-between;">
                <h4>Gerir Turma</h4>
                <button type="button" class="btn edit" onclick="document.getElementById('gerar-turma-list').classList.toggle('is-hidden')">
                    Gerir turma
                </button>
            </div>
            <div id="gerar-turma-list" class="is-hidden form-card" style="margin-top:12px;">
                <p style="margin-bottom:10px; color:#0b2447; font-weight:600;">Seleciona uma turma para editar</p>
                <form method="POST" action="{{ route('academia.turmas.edit') }}">
                    @csrf
                    <div class="form-grid">
                        @forelse ($turmas as $turma)
                            <label style="display:flex; gap:8px; align-items:center;">
                                <input type="radio" name="turma_id" value="{{ data_get($turma, 'id') }}">
                                <span>{{ data_get($turma, 'nome_turma', '-') }}</span>
                            </label>
                        @empty
                            <p>Sem turmas registadas.</p>
                        @endforelse
                    </div>
                    <div class="form-actions">
                        <button type="submit" formaction="{{ route('academia.turmas.lista.select') }}" class="btn">Ver lista</button>
                        <button type="submit" class="btn edit">Editar turma</button>
                    </div>
                </form>
            </div>
        </section>
    </main>
 

    <script src="{{ asset('js/sidebar-gradient.js') }}"></script>
    <script>
        (function () {
            const professores = @json($professoresLookup ?? []);
            const professorInput = document.getElementById('professor_nome');
            const idProfessorInput = document.getElementById('professor_id');

            if (!professorInput || !idProfessorInput) {
                return;
            }

            const normalize = (value) => (value || '').toString().trim().toLowerCase();

            const preencherIdProfessor = () => {
                const selected = professorInput.options[professorInput.selectedIndex];
                const idSelecionado = selected ? (selected.getAttribute('data-id') || '').toString().trim() : '';

                if (idSelecionado !== '') {
                    idProfessorInput.value = idSelecionado;
                    return;
                }

                const nomeSelecionado = normalize(professorInput.value);
                if (!nomeSelecionado) {
                    idProfessorInput.value = '';
                    return;
                }

                const professor = professores.find((item) => normalize(item.nome) === nomeSelecionado);
                idProfessorInput.value = professor ? (professor.id ?? '') : '';
            };

            professorInput.addEventListener('change', preencherIdProfessor);
            preencherIdProfessor();
        })();
    </script>
</body>
</html>

