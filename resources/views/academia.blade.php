<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Academia</title>

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
                <a href="{{ route('dashboard') }}"><i class="fa fa-chart-pie"></i> Dashboard</a>
            </li>
            <li class="{{ request()->routeIs('register') ? 'active' : '' }}">
                <a href="{{ route('register') }}"><i class="fa fa-id-card"></i> Cadastro</a>
            </li>
            <li class="{{ request()->routeIs('professores') ? 'active' : '' }}">
                <a href="{{ route('professores') }}"><i class="fa fa-chalkboard-teacher"></i> Professores</a>
            </li>
            <li class="{{ request()->routeIs('alunos') ? 'active' : '' }}">
                <a href="{{ route('alunos') }}"><i class="fa fa-user-graduate"></i> Alunos</a>
            </li>
            <li class="{{ request()->routeIs('academia*') ? 'active' : '' }}">
                <a href="{{ route('academia') }}"><i class="fa fa-calendar-days"></i> Academia</a>
            </li>
            <li>
                <a href="{{ route('database') }}"><i class="fa fa-database"></i> Database</a>
            </li>
            <li>
                <a href="{{ route('perfil') }}"><i class="fa fa-user"></i> Perfil</a>
            </li>
            <li>
                <a href="{{ route('logout') }}" class="logout-btn"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
            </li>
        </ul>
    </aside>

    <main class="content">
        <header class="topbar">
            <h3>Academia</h3>
        </header>

        <nav class="academia-nav">
            <a class="academia-link" href="{{ route('academia.acesso_alunos') }}">Acesso Alunos</a>
            <a class="academia-link" href="{{ route('academia.turmas') }}">Turmas</a>
            <a class="academia-link" href="{{ route('academia.acesso_professores') }}">Acesso Professores</a>
            <a class="academia-link" href="{{ route('academia.noticias') }}">Noticias</a>
            <a class="academia-link" href="{{ route('academia.administradores') }}">Administradores</a>
            <a class="academia-link" href="{{ route('academia.chat') }}">Chat</a>
        </nav>

        <section class="card">
            <h4>Menu da Academia</h4>
            @if (session('error'))
                <p style="margin-top:10px; color:#b91c1c;">{{ session('error') }}</p>
            @endif
            <p style="margin-top: 10px;">Seleciona uma opcao acima para aceder as paginas especificas.</p>
        </section>
    </main>
</div>

    <script src="{{ asset('js/sidebar-gradient.js') }}"></script>
</body>
</html>



