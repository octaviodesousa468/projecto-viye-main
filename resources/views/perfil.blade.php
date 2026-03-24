<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Perfil</title>
    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .perfil-card {
            max-width: 980px;
            background: linear-gradient(145deg, #f8fbff 0%, #eef5ff 100%);
            border: 1px solid #d6e3ff;
            border-radius: 16px;
            padding: 24px;
        }

        .perfil-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            margin-bottom: 18px;
            flex-wrap: wrap;
        }

        .perfil-title {
            margin: 0;
            font-size: 1.2rem;
            color: #0b2447;
        }

        .perfil-subtitle {
            margin: 4px 0 0;
            color: #4b5563;
            font-size: 0.92rem;
        }

        .perfil-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            border: 1px solid transparent;
        }

        .perfil-badge.ok {
            color: #166534;
            background: #ecfdf3;
            border-color: #bbf7d0;
        }

        .perfil-badge.warn {
            color: #9a3412;
            background: #fff7ed;
            border-color: #fed7aa;
        }

        .perfil-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 14px;
        }

        .perfil-item {
            background: #ffffff;
            border: 1px solid #d9e6ff;
            border-radius: 12px;
            padding: 12px 14px;
        }

        .perfil-item label {
            display: block;
            font-size: 0.78rem;
            font-weight: 700;
            color: #334155;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            margin-bottom: 6px;
        }

        .perfil-item input {
            width: 100%;
            border: 0;
            outline: none;
            background: transparent;
            color: #0f172a;
            font-size: 0.98rem;
            padding: 0;
            font-weight: 600;
        }

        .perfil-empty {
            margin: 0;
            color: #374151;
            line-height: 1.5;
        }
    </style>
</head>
<body>
<div class="container">
    <aside class="sidebar">
        <a href="{{ route('dashboard') }}"><img src="{{ asset('img/jardim-logotipo122.png') }}" alt="Imagem" width="150" height="100" class="logo"></a>
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
            <h3>Perfil do Usuario</h3>
        </header>

        <section class="card perfil-card">
            <div class="perfil-header">
                <div>
                    <h4 class="perfil-title">Dados do Usuario</h4>
                    <p class="perfil-subtitle">Informacoes da sessao atual.</p>
                </div>
                <span class="perfil-badge ok">Sessao ativa</span>
            </div>

            <div class="perfil-grid" style="margin-bottom:18px;">
                <div class="perfil-item">
                    <label>Nome</label>
                    <input type="text" value="{{ data_get($dadosUsuario ?? [], 'nome', '-') }}" readonly>
                </div>

                <div class="perfil-item">
                    <label>Email</label>
                    <input type="text" value="{{ data_get($dadosUsuario ?? [], 'email', '-') }}" readonly>
                </div>

                <div class="perfil-item">
                    <label>Login</label>
                    <input type="text" value="{{ data_get($dadosUsuario ?? [], 'login', '-') }}" readonly>
                </div>

                <div class="perfil-item">
                    <label>Nivel de acesso</label>
                    <input type="text" value="{{ data_get($dadosUsuario ?? [], 'nivel_acesso', '-') }}" readonly>
                </div>
            </div>

            <div class="perfil-header">
                <div>
                    <h4 class="perfil-title">Dados do Funcionario</h4>
                    <p class="perfil-subtitle">Informacoes cadastrais vinculadas ao usuario autenticado.</p>
                </div>
                <span class="perfil-badge {{ $perfilFuncionario ? 'ok' : 'warn' }}">
                    {{ $perfilFuncionario ? 'Perfil encontrado' : 'Perfil nao encontrado' }}
                </span>
            </div>

            @if ($perfilFuncionario)
                <div class="perfil-grid">
                    <div class="perfil-item">
                        <label>Nome</label>
                        <input type="text" value="{{ data_get($perfilFuncionario, 'nome', '-') }}" readonly>
                    </div>

                    <div class="perfil-item">
                        <label>Email</label>
                        <input type="text" value="{{ data_get($perfilFuncionario, 'email', '-') }}" readonly>
                    </div>

                    <div class="perfil-item">
                        <label>BI / Passaporte</label>
                        <input type="text" value="{{ data_get($perfilFuncionario, 'bi_passaporte', '-') }}" readonly>
                    </div>

                    <div class="perfil-item">
                        <label>Data de Nascimento</label>
                        <input type="text" value="{{ data_get($perfilFuncionario, 'data_nascimento', '-') }}" readonly>
                    </div>

                    <div class="perfil-item">
                        <label>Sexo</label>
                        <input type="text" value="{{ data_get($perfilFuncionario, 'sexo', '-') }}" readonly>
                    </div>

                    <div class="perfil-item">
                        <label>Nacionalidade</label>
                        <input type="text" value="{{ data_get($perfilFuncionario, 'nacionalidade', '-') }}" readonly>
                    </div>

                    <div class="perfil-item">
                        <label>Contacto</label>
                        <input type="text" value="{{ data_get($perfilFuncionario, 'contacto', data_get($perfilFuncionario, 'telefone', '-')) }}" readonly>
                    </div>

                    <div class="perfil-item">
                        <label>Formacao</label>
                        <input type="text" value="{{ data_get($perfilFuncionario, 'formacao', '-') }}" readonly>
                    </div>

                    <div class="perfil-item">
                        <label>Nivel Academico</label>
                        <input type="text" value="{{ data_get($perfilFuncionario, 'nivel_academico', '-') }}" readonly>
                    </div>

                    <div class="perfil-item">
                        <label>Endereco</label>
                        <input type="text" value="{{ data_get($perfilFuncionario, 'endereco', '-') }}" readonly>
                    </div>

                    <div class="perfil-item">
                        <label>Funcao</label>
                        <input type="text" value="{{ data_get($perfilFuncionario, 'funcao', '-') }}" readonly>
                    </div>

                    <div class="perfil-item">
                        <label>Departamento</label>
                        <input type="text" value="{{ data_get($perfilFuncionario, 'departamento', '-') }}" readonly>
                    </div>
                </div>
            @else
                <p class="perfil-empty">Nao foi possivel localizar o funcionario logado na tabela <strong>funcionario</strong>.</p>
            @endif
        </section>
    </main>
</div>
<script src="{{ asset('js/sidebar-gradient.js') }}"></script>
</body>
</html>

