<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cadastro Aluno</title>

    <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="container">
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}"><img src="{{ asset('img/jardim-logotipo122.png') }}" alt="Imagem" width="150px" height="100px" class="logo"></a>

        <ul>
            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a href="{{ route('dashboard') }}">
                    <i class="fa fa-chart-pie"></i> Dashboard
                </a>
            </li>
            <li class="{{ request()->routeIs('register') || request()->routeIs('register_aluno') || request()->routeIs('register_professor') || request()->routeIs('register_funcionario') ? 'active' : '' }}">
                <a href="{{ route('register') }}">
                    <i class="fa fa-id-card"></i> Cadastro
                </a>
            </li>
            <li class="{{ request()->routeIs('professores') ? 'active' : '' }}">
                <a href="{{ route('professores') }}">
                    <i class="fa fa-chalkboard-teacher"></i> Professores
                </a>
            </li>
            <li class="{{ request()->routeIs('alunos') ? 'active' : '' }}">
                <a href="{{ route('alunos') }}">
                    <i class="fa fa-user-graduate"></i> Alunos
                </a>
            </li>
            <li class="{{ request()->routeIs('academia') ? 'active' : '' }}">
                <a href="{{ route('academia') }}">
                    <i class="fa fa-calendar-days"></i> Academia
                </a>
            </li>
            <li class="{{ request()->routeIs('database') ? 'active' : '' }}">
                <a href="{{ route('database') }}">
                    <i class="fa fa-database"></i> Database
                </a>
            </li>
            <li class="{{ request()->routeIs('perfil') ? 'active' : '' }}">
                <a href="{{ route('perfil') }}">
                    <i class="fa fa-user"></i> Perfil
                </a>
            </li>
            <li>
                <a href="{{ route('logout') }}" class="logout-btn">
                    <i class="fa-solid fa-right-from-bracket"></i> Sair
                </a>
            </li>
        </ul>
    </aside>

    <main class="content">
        <header class="topbar">
            <nav class="cadastro-navbar">
                <a class="cadastro-link" href="{{ route('register') }}">Visao geral</a>
                <a class="cadastro-link is-active" href="{{ route('register_aluno') }}">Cadastro aluno</a>
                <a class="cadastro-link" href="{{ route('register_professor') }}">Cadastro professor</a>
                <a class="cadastro-link" href="{{ route('register_funcionario') }}">Cadastro funcionario</a>
            </nav>
        </header>

        <section class="form-box">
            <h2>Novo Cadastro de Aluno</h2>
            @if (session('success'))
                <p style="color:#166534; margin:10px 0;">{{ session('success') }}</p>
            @endif
            @if (session('error'))
                <p style="color:#b91c1c; margin:10px 0;">{{ session('error') }}</p>
            @endif
            @if ($errors->any())
                <div style="color:#b91c1c; margin:10px 0; text-align:left;">
                    <p>Cadastro nao foi efetuado. Verifica os campos:</p>
                    <ul style="margin:6px 0 0 18px;">
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register_aluno.store') }}">
                @csrf
                <div class="input-group">
                    <label>Nome completo</label>
                    <input type="text" name="nome" value="{{ old('nome') }}" maxlength="60" required placeholder="Digite o nome">
                </div>

                <div class="input-group">
                    <label>Data de nascimento</label>
                    <input type="date" name="data_nascimento" value="{{ old('data_nascimento') }}" required>
                </div>

                <div class="input-group">
                    <label>Sexo</label>
                    <select name="sexo" required>
                        <option value="">Selecione</option>
                        <option value="Masculino" {{ old('sexo') === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                        <option value="Feminino" {{ old('sexo') === 'Feminino' ? 'selected' : '' }}>Feminino</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>BI</label>
                    <input type="text" name="bi" value="{{ old('bi') }}" maxlength="21" required placeholder="Digite o BI">
                </div>

                <div class="input-group">
                    <label>Nacionalidade</label>
                    <input type="text" name="nacionalidade" value="{{ old('nacionalidade') }}" maxlength="20" required placeholder="Digite a nacionalidade">
                </div>

                <div class="input-group">
                    <label>Encarregados</label>
                    <input type="text" name="encarregados" value="{{ old('encarregados') }}" maxlength="40" required placeholder="Digite o nome do encarregado">
                </div>

                <div class="input-group">
                    <label>Turma</label>
                    <select name="turma" id="turma_select" required>
                        <option value="">Selecione a turma</option>
                        @foreach (($turmas ?? collect()) as $turma)
                            <option
                                value="{{ data_get($turma, 'nome_turma') }}"
                                data-id="{{ data_get($turma, 'id') }}"
                                @if (old('turma') === data_get($turma, 'nome_turma') || (string) old('id_turma') === (string) data_get($turma, 'id')) selected @endif
                            >
                                {{ data_get($turma, 'nome_turma') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <input type="hidden" id="id_turma" name="id_turma" value="{{ old('id_turma') }}">

                

                <div class="input-group">
                    <label>Contacto encarregado</label>
                    <input type="number" name="contactoencarregado" value="{{ old('contactoencarregado') }}" min="0" max="999999999" step="1" required placeholder="Digite o contacto do encarregado">
                </div>

                <div class="input-group">
                    <label>Contacto alternativo</label>
                    <input type="number" name="contactoalternativo" value="{{ old('contactoalternativo') }}" min="0" max="999999999" step="1" required placeholder="Digite o contacto alternativo">
                </div>

                <button type="submit">Cadastrar aluno</button>
            </form>
        </section>
    </main>
</div>

    <script src="{{ asset('js/sidebar-gradient.js') }}"></script>
    <script>
        (function () {
            const turmaSelect = document.getElementById('turma_select');
            const idTurmaInput = document.getElementById('id_turma');
            if (!turmaSelect || !idTurmaInput) return;

            const syncIdTurma = () => {
                const selected = turmaSelect.options[turmaSelect.selectedIndex];
                idTurmaInput.value = selected ? (selected.getAttribute('data-id') || '') : '';
            };

            turmaSelect.addEventListener('change', syncIdTurma);
            syncIdTurma();
        })();
    </script>
</body>
</html>




