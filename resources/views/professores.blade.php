<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Professores</title>

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
            <h3>Professores</h3>
        </header>

        <section class="card">
            <div style="display:flex; justify-content:space-between; margin-bottom:15px;">
                <h4>Lista de Professores</h4>
                <a href="{{ route('register_professor') }}" class="btn">+ Novo Professor</a>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Email</th>
                        <th>Contacto</th>
                        <th>Turma</th>
                        <th>Formação</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($professores as $professor)
                        <tr>
                            <td>{{ data_get($professor, 'id', '-') }}</td>
                            <td>{{ data_get($professor, 'nome', '-') }}</td>
                            <td>{{ data_get($professor, 'email', '-') }}</td>
                            <td>{{ data_get($professor, 'contacto', data_get($professor, 'telefone', '-')) }}</td>
                            <td>{{ data_get($professor, 'turma', '-') }}</td>
                            <td>{{ data_get($professor, 'formação', data_get($professor, 'formacao', '-')) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align:center;">Sem professores cadastrados.</td>
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



