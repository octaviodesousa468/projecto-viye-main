
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Database</title>

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .search-row {
            display: flex;
            gap: 10px;
            margin-top: 12px;
        }
        .search-row input {
            flex: 1;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #cbd5e1;
        }
        .action-wrap {
            display: flex;
            gap: 8px;
        }
        .btn.delete {
            background: #b91c1c;
            color: #fff;
        }
        .btn.delete:hover {
            background: #991b1b;
        }
        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 999px;
            background: #e2e8f0;
            font-size: 12px;
            font-weight: 600;
        }
        .name-toggle {
            background: transparent;
            border: none;
            color: #0b2447;
            font-weight: 700;
            cursor: pointer;
            text-decoration: underline;
            padding: 0;
        }
        .details-row {
            display: none;
            background: #f8fafc;
        }
        .details-box {
            text-align: left;
            padding: 12px;
        }
        .filter-row {
            display: flex;
            gap: 10px;
            margin-top: 14px;
            flex-wrap: wrap;
        }
        .btn.filter-active {
            background: #0b2447;
            color: #fff;
        }
    </style>
</head>
<body>
<div class="container">
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}"><img src="{{ asset('img/jardim-logotipo122.png') }}" alt="Imagem" width="150px" height="100px" class="logo"></a>
        <ul>
            <li class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><a href="{{ route('dashboard') }}"><i class="fa fa-chart-pie"></i> Dashboard</a></li>
            <li><a href="{{ route('register') }}"><i class="fa fa-id-card"></i> Cadastro</a></li>
            <li class="{{ request()->routeIs('professores') ? 'active' : '' }}"><a href="{{ route('professores') }}"><i class="fa fa-chalkboard-teacher"></i> Professores</a></li>
            <li class="{{ request()->routeIs('alunos') ? 'active' : '' }}"><a href="{{ route('alunos') }}"><i class="fa fa-user-graduate"></i> Alunos</a></li>
            <li class="{{ request()->routeIs('academia') ? 'active' : '' }}"><a href="{{ route('academia') }}"><i class="fa fa-calendar-days"></i> Academia</a></li>
            <li class="{{ request()->routeIs('database') ? 'active' : '' }}"><a href="{{ route('database') }}"><i class="fa fa-database"></i> Database</a></li>
            <li><a href="{{ route('perfil') }}"><i class="fa fa-user"></i> Perfil</a></li>
            <li><a href="{{ route('logout') }}" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Sair</a></li>
        </ul>
    </aside>

    <main class="content">
        <header class="topbar">
            <h3>Database - Pesquisa Geral</h3>
        </header>

        <section class="card">
            <h4>Pesquisar Aluno, Professor ou Funcionario</h4>
            <form method="GET" action="{{ route('database') }}" class="search-row">
                <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Digite o nome...">
                <button type="submit" class="btn edit">Pesquisar</button>
            </form>
            <div class="filter-row">
                <a href="{{ route('database', ['lista' => 'aluno']) }}" class="btn {{ ($lista ?? '') === 'aluno' ? 'filter-active' : 'edit' }}">Alunos</a>
                <a href="{{ route('database', ['lista' => 'professor']) }}" class="btn {{ ($lista ?? '') === 'professor' ? 'filter-active' : 'edit' }}">Professores</a>
                <a href="{{ route('database', ['lista' => 'funcionario']) }}" class="btn {{ ($lista ?? '') === 'funcionario' ? 'filter-active' : 'edit' }}">Funcionarios</a>
            </div>
        </section>

        @if (!empty($lista))
            <section class="card" style="margin-top:16px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
                    <h4>{{ $listaTitulo ?? 'Lista' }}</h4>
                    <span>Total: {{ ($listaRegistros ?? collect())->count() }}</span>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Nome</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse (($listaRegistros ?? collect()) as $item)
                            <tr>
                                <td><span class="badge">{{ data_get($item, 'tipo') }}</span></td>
                                <td>
                                    <button type="button" class="name-toggle" data-target="lista-detalhes-{{ $loop->index }}">
                                        {{ data_get($item, 'nome', '-') }}
                                    </button>
                                </td>
                                <td>
                                    <div class="action-wrap">
                                        <a class="btn edit" href="{{ route('database.edit', ['tipo' => data_get($item, 'tipo_key'), 'id' => data_get($item, 'id')]) }}">Editar</a>
                                        <form method="POST" action="{{ route('database.destroy', ['tipo' => data_get($item, 'tipo_key'), 'id' => data_get($item, 'id')]) }}" onsubmit="return confirm('Tem certeza que deseja eliminar este registro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn delete">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <tr id="lista-detalhes-{{ $loop->index }}" class="details-row">
                                <td colspan="3" class="details-box">
                                    @foreach (data_get($item, 'dados', []) as $campo => $valor)
                                        @if (!in_array($campo, ['password', 'created_at', 'updated_at']))
                                            <div><strong>{{ $campo }}:</strong> {{ $valor ?? '-' }}</div>
                                        @endif
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center;">Nenhum registro encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        @endif

        @if (!empty($q))
            <section class="card" style="margin-top:16px;">
                <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
                    <h4>Resultados</h4>
                    <span>Total: {{ $resultados->count() }}</span>
                </div>

                <table class="table">
                    <thead>
                        <tr>
                            <th>Tipo</th>
                            <th>Nome</th>
                            <th>Acoes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($resultados as $item)
                            <tr>
                                <td><span class="badge">{{ data_get($item, 'tipo') }}</span></td>
                                <td>
                                    <button type="button" class="name-toggle" data-target="resultado-detalhes-{{ $loop->index }}">
                                        {{ data_get($item, 'nome', '-') }}
                                    </button>
                                </td>
                                <td>
                                    <div class="action-wrap">
                                        <a class="btn edit" href="{{ route('database.edit', ['tipo' => data_get($item, 'tipo_key'), 'id' => data_get($item, 'id')]) }}">Editar</a>
                                        <form method="POST" action="{{ route('database.destroy', ['tipo' => data_get($item, 'tipo_key'), 'id' => data_get($item, 'id')]) }}" onsubmit="return confirm('Tem certeza que deseja eliminar este registro?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn delete">Eliminar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            <tr id="resultado-detalhes-{{ $loop->index }}" class="details-row">
                                <td colspan="3" class="details-box">
                                    @foreach (data_get($item, 'dados', []) as $campo => $valor)
                                        @if (!in_array($campo, ['password', 'created_at', 'updated_at']))
                                            <div><strong>{{ $campo }}:</strong> {{ $valor ?? '-' }}</div>
                                        @endif
                                    @endforeach
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align:center;">Nenhum resultado encontrado.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>
        @endif
    </main>
</div>

<script src="{{ asset('js/sidebar-gradient.js') }}"></script>
<script>
    document.querySelectorAll('.name-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            const targetId = button.getAttribute('data-target');
            const row = document.getElementById(targetId);
            if (!row) return;
            row.style.display = row.style.display === 'table-row' ? 'none' : 'table-row';
        });
    });
</script>
</body>
</html>

