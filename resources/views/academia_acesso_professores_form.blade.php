<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>{{ $isEdit ? 'Editar' : 'Criar' }} Acesso Professor</title>
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">
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
        </ul>
    </aside>

    <main class="content">
        <header class="topbar"><h3>{{ $isEdit ? 'Editar' : 'Criar' }} Acesso Professor</h3></header>
        <nav class="academia-nav">
            <a class="academia-link" href="{{ route('academia.acesso_alunos') }}">Acesso Alunos</a>
            <a class="academia-link" href="{{ route('academia.turmas') }}">Turmas</a>
            <a class="academia-link is-active" href="{{ route('academia.acesso_professores') }}">Acesso Professores</a>
            <a class="academia-link" href="{{ route('academia.noticias') }}">Noticias</a>
            <a class="academia-link" href="{{ route('academia.administradores') }}">Administradores</a>
        </nav>

        <section class="card" style="max-width:760px;">
            @if ($errors->any())
                <p style="color:#b91c1c; margin-bottom:12px;">Verifica os campos do formulario.</p>
            @endif
            <form method="POST" action="{{ $isEdit ? route('academia.acesso_professores.update', data_get($professor, 'id')) : route('academia.acesso_professores.store') }}">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif
                @if ($isEdit)
                    <div class="input-group">
                        <label>Email do Professor</label>
                        <input type="email" name="email_professor" value="{{ old('email_professor', data_get($professor, 'email_professor')) }}">
                    </div>
                    <div class="input-group">
                        <label>Status de Acesso</label>
                        <select name="acesso">
                            <option value="ativo" {{ old('acesso', data_get($professor, 'acesso', 'ativo')) === 'ativo' ? 'selected' : '' }}>ativo</option>
                            <option value="inativo" {{ old('acesso', data_get($professor, 'acesso')) === 'inativo' ? 'selected' : '' }}>inativo</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Senha (opcional para alterar)</label>
                        <input type="password" name="password">
                    </div>
                @else
                    <div class="input-group">
                        <label>Email do Professor</label>
                        <input type="email" name="email_professor" value="{{ old('email_professor') }}" required>
                    </div>
                    <div class="input-group">
                        <label>Palavra-passe</label>
                        <input type="password" name="password" required>
                    </div>
                @endif
                <div class="input-group">
                    <label>Perfil do Professor</label>
                    <select name="perfil_professor">
                        <option value="">Seleciona um professor</option>
                        @foreach(($perfisProfessor ?? collect()) as $perfil)
                            @php $nomeProfessor = (string) data_get($perfil, 'nome', ''); @endphp
                            <option value="{{ $nomeProfessor }}" {{ old('perfil_professor') === $nomeProfessor ? 'selected' : '' }}>
                                {{ $nomeProfessor }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex; gap:10px; margin-top:10px;">
                    <button type="submit" class="btn edit">{{ $isEdit ? 'Salvar Alteracoes' : 'Criar Acesso' }}</button>
                    <a href="{{ route('academia.acesso_professores') }}" class="btn">Cancelar</a>
                </div>
            </form>
        </section>
    </main>
</div>
    <script src="{{ asset('js/sidebar-gradient.js') }}"></script>
</body>
</html>

