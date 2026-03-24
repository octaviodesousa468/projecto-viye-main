<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Academia - Noticias</title>

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .is-hidden { display: none; }
        .form-card {
            background: #f8fbff;
            border: 1px solid #d6e3ff;
            border-radius: 14px;
            padding: 16px;
            margin-top: 12px;
        }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }
        .input-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 6px;
            color: #0b2447;
        }
        .input-group input,
        .input-group select,
        .input-group textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #c9d6f2;
            background: #fff;
            outline: none;
        }
        .input-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        .input-group.full {
            grid-column: 1 / -1;
        }
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 12px;
        }
        .notice-list {
            margin-top: 16px;
            display: grid;
            gap: 12px;
        }
        .notice-item {
            border: 1px solid #dbe6fb;
            border-radius: 12px;
            padding: 14px;
            background: #ffffff;
        }
        .notice-meta {
            font-size: 13px;
            color: #475569;
            margin-top: 6px;
        }
        .notice-body {
            margin-top: 8px;
            white-space: pre-wrap;
        }
        .notice-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .btn-danger {
            border: 1px solid #dc2626;
            background: #dc2626;
            color: #fff;
            border-radius: 8px;
            padding: 8px 12px;
            cursor: pointer;
            font-weight: 600;
        }
        .btn-danger:hover {
            background: #b91c1c;
            border-color: #b91c1c;
        }
        @media (max-width: 720px) {
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
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
            <h3>Academia - Noticias</h3>
        </header>

        <nav class="academia-nav">
            <a class="academia-link" href="{{ route('academia.acesso_alunos') }}">Acesso Alunos</a>
            <a class="academia-link" href="{{ route('academia.turmas') }}">Turmas</a>
            <a class="academia-link" href="{{ route('academia.acesso_professores') }}">Acesso Professores</a>
            <a class="academia-link is-active" href="{{ route('academia.noticias') }}">Noticias</a>
            <a class="academia-link" href="{{ route('academia.administradores') }}">Administradores</a>
        </nav>

        <section class="card">
            <h4>Noticias da Academia</h4>
            @if (session('success'))
                <p style="color:#166534; margin-top:10px;">{{ session('success') }}</p>
            @endif
            @if ($errors->any())
                <p style="color:#b91c1c; margin-top:10px;">Verifica os campos da noticia e tenta novamente.</p>
            @endif

            <div style="margin-top:14px;">
                <button type="button" class="btn edit" id="abrir-form-noticia">Criar noticia</button>
            </div>

            <form id="form-noticia" class="form-card is-hidden" method="POST" action="{{ route('academia.noticias.store') }}">
                @csrf
                <div class="form-grid">
                    <div class="input-group">
                        <label>Assunto</label>
                        <input type="text" name="assunto" value="{{ old('assunto') }}" maxlength="120" required>
                    </div>
                    <div class="input-group">
                        <label>Data</label>
                        <input type="date" name="data" value="{{ old('data') }}" required>
                    </div>
                    <div class="input-group">
                        <label>Hora</label>
                        <input type="time" name="hora" value="{{ old('hora') }}" required>
                    </div>
                    <div class="input-group">
                        <label>Destinatario</label>
                        <select name="destinatario" required>
                            <option value="">Seleciona um destinatario</option>
                            <option value="alunos" {{ old('destinatario') === 'alunos' ? 'selected' : '' }}>So alunos</option>
                            <option value="professores" {{ old('destinatario') === 'professores' ? 'selected' : '' }}>So professores</option>
                            <option value="geral" {{ old('destinatario') === 'geral' ? 'selected' : '' }}>Noticia Geral</option>
                        </select>
                    </div>
                    <div class="input-group full">
                        <label>Nota</label>
                        <textarea name="nota" maxlength="2000" required>{{ old('nota') }}</textarea>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn edit">Publicar</button>
                </div>
            </form>

            <div class="notice-list">
                @forelse (($noticias ?? collect()) as $noticia)
                    <article class="notice-item">
                        <h4>{{ data_get($noticia, 'assunto', '-') }}</h4>
                        <p class="notice-meta">
                            Data: {{ data_get($noticia, 'data_publicacao', '-') }}
                            | Hora: {{ data_get($noticia, 'hora_publicacao', '-') }}
                            | Destinatario:
                            @if (data_get($noticia, 'destinatario') === 'alunos')
                                So alunos
                            @elseif (data_get($noticia, 'destinatario') === 'professores')
                                So professores
                            @else
                                Noticia Geral
                            @endif
                        </p>
                        <p class="notice-body">{{ data_get($noticia, 'nota', '-') }}</p>

                        <div class="notice-actions">
                            <button
                                type="button"
                                class="btn edit js-toggle-edit-noticia"
                                data-target="form-editar-noticia-{{ data_get($noticia, 'id') }}"
                            >
                                Editar
                            </button>
                            <form method="POST" action="{{ route('academia.noticias.destroy', ['id' => data_get($noticia, 'id')]) }}">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="btn-danger"
                                    onclick="return confirm('Tens certeza que queres eliminar esta noticia?');"
                                >
                                    Eliminar
                                </button>
                            </form>
                        </div>

                        <form
                            id="form-editar-noticia-{{ data_get($noticia, 'id') }}"
                            class="form-card is-hidden"
                            method="POST"
                            action="{{ route('academia.noticias.update', ['id' => data_get($noticia, 'id')]) }}"
                        >
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="edit_noticia_id" value="{{ data_get($noticia, 'id') }}">
                            <div class="form-grid">
                                <div class="input-group">
                                    <label>Assunto</label>
                                    <input type="text" name="assunto" value="{{ old('edit_noticia_id') == data_get($noticia, 'id') ? old('assunto') : data_get($noticia, 'assunto') }}" maxlength="120" required>
                                </div>
                                <div class="input-group">
                                    <label>Data</label>
                                    <input type="date" name="data" value="{{ old('edit_noticia_id') == data_get($noticia, 'id') ? old('data') : data_get($noticia, 'data_publicacao') }}" required>
                                </div>
                                <div class="input-group">
                                    <label>Hora</label>
                                    <input type="time" name="hora" value="{{ old('edit_noticia_id') == data_get($noticia, 'id') ? old('hora') : data_get($noticia, 'hora_publicacao') }}" required>
                                </div>
                                <div class="input-group">
                                    <label>Destinatario</label>
                                    <select name="destinatario" required>
                                        @php
                                            $destinatarioAtual = old('edit_noticia_id') == data_get($noticia, 'id')
                                                ? old('destinatario')
                                                : data_get($noticia, 'destinatario');
                                        @endphp
                                        <option value="alunos" {{ $destinatarioAtual === 'alunos' ? 'selected' : '' }}>So alunos</option>
                                        <option value="professores" {{ $destinatarioAtual === 'professores' ? 'selected' : '' }}>So professores</option>
                                        <option value="geral" {{ $destinatarioAtual === 'geral' ? 'selected' : '' }}>Noticia Geral</option>
                                    </select>
                                </div>
                                <div class="input-group full">
                                    <label>Nota</label>
                                    <textarea name="nota" maxlength="2000" required>{{ old('edit_noticia_id') == data_get($noticia, 'id') ? old('nota') : data_get($noticia, 'nota') }}</textarea>
                                </div>
                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn edit">Guardar alteracoes</button>
                            </div>
                        </form>
                    </article>
                @empty
                    <p style="margin-top:12px;">Sem noticias publicadas.</p>
                @endforelse
            </div>
        </section>
    </main>
</div>

    <script src="{{ asset('js/sidebar-gradient.js') }}"></script>
    <script>
        (function () {
            const button = document.getElementById('abrir-form-noticia');
            const form = document.getElementById('form-noticia');
            const botoesEditar = document.querySelectorAll('.js-toggle-edit-noticia');

            if (!button || !form) {
                return;
            }

            const oldEditId = @json(old('edit_noticia_id'));
            const houveErroCriacao = @json($errors->any() && !old('edit_noticia_id'));

            if (houveErroCriacao || @json(old('assunto') || old('data') || old('hora') || old('destinatario') || old('nota'))) {
                form.classList.remove('is-hidden');
            }

            if (oldEditId) {
                const formEdicaoComErro = document.getElementById(`form-editar-noticia-${oldEditId}`);
                if (formEdicaoComErro) {
                    formEdicaoComErro.classList.remove('is-hidden');
                }
            }

            button.addEventListener('click', () => {
                form.classList.toggle('is-hidden');
            });

            botoesEditar.forEach((botao) => {
                botao.addEventListener('click', () => {
                    const targetId = botao.getAttribute('data-target');
                    const targetForm = targetId ? document.getElementById(targetId) : null;
                    if (!targetForm) {
                        return;
                    }
                    targetForm.classList.toggle('is-hidden');
                });
            });
        })();
    </script>
</body>
</html>

