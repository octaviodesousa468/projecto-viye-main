<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="container">

    <!-- SIDEBAR -->
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

            <!-- ÃCONE DE AGENDA -->
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


    <!-- CONTEÃšDO -->
    <main class="content">

        <!-- TOPO -->
        <header class="topbar">
            <h3>Dashboard</h3>
        </header>

        <!-- CARDS -->
        <section class="cards">
            <div class="card">
                <i class="fa fa-user-graduate icon blue"></i>
                <p>Alunos</p>
                <h2>{{ $totalAlunos }}</h2>
            </div>

            <div class="card">
                <i class="fa fa-chalkboard-teacher icon green"></i>
                <p>Professores</p>
                <h2>{{ $totalProfessores }}</h2>
            </div>

            <div class="card">
                <i class="fa fa-book icon purple"></i>
                <p>Turmas</p>
                <h2>{{ $totalTurmas }}</h2>
            </div>

            <div class="card">
                <i class="fa fa-briefcase icon orange"></i>
                <p>Funcionarios</p>
                <h2>{{ $totalFuncionarios }}</h2>
            </div>

        </section>

    </main>

    <script src="{{ asset('js/sidebar-gradient.js') }}"></script>
</body>
</html>



