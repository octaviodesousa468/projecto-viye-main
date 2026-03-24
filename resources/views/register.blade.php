<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cadastro</title>

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
                <a class="cadastro-link is-active" href="{{ route('register') }}">Visao geral</a>
                <a class="cadastro-link" href="{{ route('register_aluno') }}">Cadastro aluno</a>
                <a class="cadastro-link" href="{{ route('register_professor') }}">Cadastro professor</a>
                <a class="cadastro-link" href="{{ route('register_funcionario') }}">Cadastro funcionario</a>
            </nav>
        </header>

        <section class="form-box">
            <h2>Centro de Cadastro</h2>
            <p class="cadastro-subtitle">Escolhe uma pagina para cadastrar por tipo de utilizador.</p>

            <div class="cadastro-cards">
                <a class="cadastro-card" href="{{ route('register_aluno') }}">
                    <strong>Cadastro de Aluno</strong>
                    Registrar estudantes com nome, curso e contacto.
                </a>
                <a class="cadastro-card" href="{{ route('register_professor') }}">
                    <strong>Cadastro de Professor</strong>
                    Registrar docentes e disciplina principal.
                </a>
                <a class="cadastro-card" href="{{ route('register_funcionario') }}">
                    <strong>Cadastro de Funcionario</strong>
                    Registrar pessoal administrativo e funcao.
                </a>
            </div>
        </section>
    </main>
</div>

    <script src="{{ asset('js/sidebar-gradient.js') }}"></script>
</body>
</html>



