<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Alunos</title>

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
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

            <li>
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

            <li>
                <a href="{{ route('database') }}">
                    <i class="fa fa-database"></i> Database
                </a>
            </li>


            <li>
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
            <h3>Alunos</h3>
        </header>

        <section class="card">
            <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
                <h4>Lista de Alunos</h4>
                <a href="{{ route('register_aluno') }}" class="btn">+ Novo Aluno</a>
            </div>
            <p style="margin:0 0 12px; color:#475569;">Clique no nome do aluno para editar os dados.</p>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Idade</th>
                        <th>Data Nascimento</th>
                        <th>Nacionalidade</th>
                        <th>Turma</th>
                        <th>Contacto Encarregado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($alunos as $aluno)
                        <tr>
                            <td>{{ data_get($aluno, 'id', '-') }}</td>
                            <td>
                                <a href="{{ route('database.edit', ['tipo' => 'aluno', 'id' => data_get($aluno, 'id')]) }}" style="font-weight:600; color:#0b2447; text-decoration:underline;">
                                    {{ data_get($aluno, 'nome', '-') }}
                                </a>
                            </td>
                            <td>{{ data_get($aluno, 'idade', '-') }}</td>
                            <td>{{ data_get($aluno, 'data_nascimento', '-') }}</td>
                            <td>{{ data_get($aluno, 'nacionalidade', '-') }}</td>
                            <td>{{ data_get($aluno, 'turma', '-') }}</td>
                            <td>{{ data_get($aluno, 'contactoencarregado', '-') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;">Sem alunos cadastrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>
    </main>
</div>

    <script src="{{ asset('js/sidebar-gradient.js') }}"></script>
</body>
</html>



