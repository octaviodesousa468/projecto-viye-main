<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Academia - Acesso Professores</title>

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
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
            <h3>Academia - Acesso Professores</h3>
        </header>

        <nav class="academia-nav">
            <a class="academia-link" href="{{ route('academia.acesso_alunos') }}">Acesso Alunos</a>
            <a class="academia-link" href="{{ route('academia.turmas') }}">Turmas</a>
            <a class="academia-link is-active" href="{{ route('academia.acesso_professores') }}">Acesso Professores</a>
            <a class="academia-link" href="{{ route('academia.noticias') }}">Noticias</a>
            <a class="academia-link" href="{{ route('academia.administradores') }}">Administradores</a>
        </nav>

        <section class="card">
            @if (session('success'))
                <p style="color:#166534; margin-bottom:10px;">{{ session('success') }}</p>
            @endif
            <h4>Lista de Professores</h4>
            <div style="display:flex; gap:10px; justify-content:space-between; align-items:center; margin-top:12px;">
                <form method="GET" action="{{ route('academia.acesso_professores') }}" style="display:flex; gap:8px;">
                    <input type="text" name="q" value="{{ $q ?? '' }}" placeholder="Pesquisar por email ou status..." style="padding:8px 10px; border:1px solid #ccc; border-radius:6px;">
                    <button type="submit" class="btn edit">Pesquisar</button>
                </form>
                <a href="{{ route('academia.acesso_professores.create') }}" class="btn edit">+ Criar Acesso</a>
            </div>
            <table class="table" style="margin-top:12px;">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email do Professor</th>
                        <th>Status Acesso</th>
                        <th>Criado em</th>
                        <th>Acoes</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($professores as $professor)
                        <tr>
                            <td>{{ data_get($professor, 'id', '-') }}</td>
                            <td>{{ data_get($professor, 'email_professor', '-') }}</td>
                            <td>{{ data_get($professor, 'acesso', '-') }}</td>
                            <td>{{ data_get($professor, 'created_at', '-') }}</td>
                            <td style="display:flex; gap:6px;">
                                <a href="{{ route('academia.acesso_professores.edit', data_get($professor, 'id')) }}" class="btn edit">Editar</a>
                                <form method="POST" action="{{ route('academia.acesso_professores.destroy', data_get($professor, 'id')) }}" onsubmit="return confirm('Deseja deletar este acesso?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn delete">Deletar</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align:center;">Sem professores cadastrados.</td>
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



