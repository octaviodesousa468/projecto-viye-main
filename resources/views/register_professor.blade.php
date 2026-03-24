<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Cadastro Professor</title>

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
                <a class="cadastro-link" href="{{ route('register_aluno') }}">Cadastro aluno</a>
                <a class="cadastro-link is-active" href="{{ route('register_professor') }}">Cadastro professor</a>
                <a class="cadastro-link" href="{{ route('register_funcionario') }}">Cadastro funcionario</a>
            </nav>
        </header>

        <section class="form-box">
            <h2>Novo Cadastro de Professor</h2>
            @if (session('success'))
                <p style="color:#166534; margin:10px 0;">{{ session('success') }}</p>
            @endif
            @if (session('error'))
                <p style="color:#b91c1c; margin:10px 0;">{{ session('error') }}</p>
            @endif
            @if ($errors->any())
                <p style="color:#b91c1c; margin:10px 0;">Cadastro nao foi efetuado. Verifica os campos do formulario.</p>
            @endif

            <form method="POST" action="{{ route('register_professor.store') }}">
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
                        <option value="Femenino" {{ old('sexo') === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                    </select>
                </div>

                <div class="input-group">
                    <label>Nacionalidade</label>
                    <input type="text" name="nacionalidade" value="{{ old('nacionalidade', 'Angola') }}" maxlength="20" required placeholder="Digite a nacionalidade">
                </div>

                <div class="input-group">
                    <label>BI / Passaporte</label>
                    <input type="text" name="bi_passaporte" value="{{ old('bi_passaporte') }}" required placeholder="Digite o nÃºmero do BI ou Passa Porte">
                </div>

                <div class="input-group">
                    <label>Contacto</label>
                    <input type="number" name="contacto" value="{{ old('contacto') }}" step="1" required placeholder="Digite o contacto (+244)">
                </div>

                <div class="input-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" maxlength="35" required placeholder="Digite o email">
                </div>

                <div class="input-group">
                    <label>Formação</label>
                    <input type="text" name="formacao" value="{{ old('formacao') }}" maxlength="90" required placeholder="Digite a formação">
                </div>

                <div class="input-group">
                    <label>Nível Académico</label>
                    <input type="text" name="nivel_academico" value="{{ old('nivel_academico') }}" maxlength="25" required placeholder="Digite o nivel academico">
                </div>

                <div class="input-group">
                    <label>Endereço</label>
                    <input type="text" name="endereco" value="{{ old('endereco') }}" required placeholder="Digite o endereço">
                </div>

                <div class="input-group">
                    <label>Turma</label>
                    <input type="text" name="turma" value="{{ old('turma') }}" maxlength="30" required placeholder="Digite a turma">
                </div>

                <button type="submit">Cadastrar professor</button>
            </form>
        </section>
    </main>
</div>

    <script src="{{ asset('js/sidebar-gradient.js') }}"></script>
</body>
</html>




