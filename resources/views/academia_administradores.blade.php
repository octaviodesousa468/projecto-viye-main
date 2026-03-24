<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Academia - Administradores</title>

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
        }
        .input-group { margin-bottom: 10px; }
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
        .panel-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 12px;
        }
    </style>
</head>
<body>

<div class="container">
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
        <header class="topbar">
            <h3>Academia - Administradores</h3>
        </header>

        <nav class="academia-nav">
            <a class="academia-link" href="{{ route('academia.acesso_alunos') }}">Acesso Alunos</a>
            <a class="academia-link" href="{{ route('academia.turmas') }}">Turmas</a>
            <a class="academia-link" href="{{ route('academia.acesso_professores') }}">Acesso Professores</a>
            <a class="academia-link" href="{{ route('academia.noticias') }}">Noticias</a>
            <a class="academia-link is-active" href="{{ route('academia.administradores') }}">Administradores</a>
        </nav>

        <section class="card">
            @if (session('success'))
                <p style="color:#166534; margin-bottom:10px;">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                <p style="color:#b91c1c; margin-bottom:10px;">{{ $errors->first() }}</p>
            @endif

            <div class="panel-actions">
                <button type="button" class="btn edit" id="btn-add-admin">Adicionar admin</button>
                <button type="button" class="btn delete" id="btn-remove-admin">Remover admin</button>
                <button type="button" class="btn" id="btn-list-admin">Lista de admin</button>
            </div>

            <form id="search-form" method="GET" action="{{ route('academia.administradores') }}" style="display:flex; gap:8px; margin-top:14px; flex-wrap:wrap;">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Pesquisar na tabela acesso_admin..." style="padding:8px 10px; border:1px solid #ccc; border-radius:6px; flex:1;">
                <button type="submit" class="btn edit">Pesquisar</button>
            </form>

            <form id="add-admin-panel" class="form-card is-hidden" method="POST" action="{{ route('academia.administradores.store') }}">
                @csrf
                <div class="input-group">
                    <label>Nome do funcionario</label>
                    <input type="text" name="nome_funcionario" list="lista-funcionarios" value="{{ old('nome_funcionario') }}" required placeholder="Digite o nome do funcionario">
                    <datalist id="lista-funcionarios">
                        @foreach (($funcionarios ?? collect()) as $funcionario)
                            <option value="{{ data_get($funcionario, 'nome') }}"></option>
                        @endforeach
                    </datalist>
                </div>
                <div class="input-group">
                    <label>Email do administrador</label>
                    <input type="email" name="email_encarregado" value="{{ old('email_encarregado') }}" required>
                </div>
                <div class="input-group">
                    <label>Palavra-passe</label>
                    <input type="password" name="password" required>
                </div>
                <div class="input-group">
                    <label>Status de acesso</label>
                    <select name="acesso">
                        <option value="ativo">ativo</option>
                        <option value="inativo">inativo</option>
                    </select>
                </div>
                <button type="submit" class="btn edit">Guardar administrador</button>
            </form>

            <div id="remove-admin-panel" class="form-card is-hidden">
                <h4 style="margin-bottom:10px;">Seleciona e remove um administrador</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Acao</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($admins ?? collect()) as $admin)
                            <tr>
                                <td>{{ data_get($admin, 'id', '-') }}</td>
                                <td>{{ data_get($admin, 'email_encarregado', data_get($admin, 'email', '-')) }}</td>
                                <td>{{ data_get($admin, 'acesso', '-') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('academia.administradores.destroy', data_get($admin, 'id')) }}" onsubmit="return confirm('Deseja remover este administrador?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn delete">Remover</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center;">Sem administradores encontrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div id="list-admin-panel" class="form-card">
                <h4 style="margin-bottom:10px;">Lista de admin</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Criado em</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($admins ?? collect()) as $admin)
                            <tr>
                                <td>{{ data_get($admin, 'id', '-') }}</td>
                                <td>{{ data_get($admin, 'email_encarregado', data_get($admin, 'email', '-')) }}</td>
                                <td>{{ data_get($admin, 'acesso', '-') }}</td>
                                <td>{{ data_get($admin, 'created_at', '-') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align:center;">Sem administradores cadastrados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

    <script src="{{ asset('js/sidebar-gradient.js') }}"></script>
    <script>
        (function () {
            const btnAdd = document.getElementById('btn-add-admin');
            const btnRemove = document.getElementById('btn-remove-admin');
            const btnList = document.getElementById('btn-list-admin');
            const addPanel = document.getElementById('add-admin-panel');
            const removePanel = document.getElementById('remove-admin-panel');
            const listPanel = document.getElementById('list-admin-panel');

            if (!btnAdd || !btnRemove || !btnList || !addPanel || !removePanel || !listPanel) {
                return;
            }

            btnAdd.addEventListener('click', () => {
                addPanel.classList.toggle('is-hidden');
                removePanel.classList.add('is-hidden');
                listPanel.classList.remove('is-hidden');
            });

            btnRemove.addEventListener('click', () => {
                removePanel.classList.toggle('is-hidden');
                addPanel.classList.add('is-hidden');
                listPanel.classList.remove('is-hidden');
            });

            btnList.addEventListener('click', () => {
                listPanel.classList.remove('is-hidden');
                addPanel.classList.add('is-hidden');
                removePanel.classList.add('is-hidden');
            });
        })();
    </script>
</body>
</html>

