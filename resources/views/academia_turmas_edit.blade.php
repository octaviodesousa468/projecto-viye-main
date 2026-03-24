<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Turma</title>

    <link rel="stylesheet" href="{{ asset('css/sidebar.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
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
        .input-group input {
            width: 100%;
            padding: 10px 12px;
            border-radius: 10px;
            border: 1px solid #c9d6f2;
            background: #fff;
            outline: none;
        }
        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 14px;
        }
        @media (max-width: 720px) {
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <main class="content">
        <header class="topbar">
            <h3>Editar Turma</h3>
        </header>

        <section class="card">
            <form method="POST" action="{{ route('academia.turmas.update', data_get($turma, 'id')) }}" class="form-card">
                @csrf
                <div class="form-grid">
                    <div class="input-group">
                        <label>Nome da turma</label>
                        <input type="text" name="nome_turma" value="{{ old('nome_turma', data_get($turma, 'nome_turma')) }}">
                    </div>
                    <div class="input-group">
                        <label>Idade dos alunos</label>
                        <input type="text" name="idade_alunos" value="{{ old('idade_alunos', data_get($turma, 'idade_alunos')) }}">
                    </div>
                    <div class="input-group">
                        <label>Professor</label>
                        <input type="text" name="professor" id="professor_nome" value="{{ old('professor', data_get($turma, 'professor')) }}">
                    </div>
                    <div class="input-group">
                        <label>ID do professor</label>
                        <input type="text" name="id_professor" id="id_professor" value="{{ old('id_professor', data_get($turma, 'id_professor', data_get($turma, 'professor_id'))) }}" placeholder="Preenchido automaticamente" readonly>
                    </div>
                    <div class="input-group">
                        <label>Professor auxiliar</label>
                        <input type="text" name="professor_auxiliar" value="{{ old('professor_auxiliar', data_get($turma, 'professor_auxiliar')) }}">
                    </div>
                    <div class="input-group">
                        <label>Tempo de aula diaria</label>
                        <input type="text" name="tempo_aula_diaria" value="{{ old('tempo_aula_diaria', data_get($turma, 'tempo_aula_diaria')) }}">
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn edit">Guardar alteracoes</button>
                    <a href="{{ route('academia.turmas') }}" class="btn">Voltar</a>
                </div>
            </form>
        </section>
    </main>

    <script>
        (function () {
            const professores = @json($professoresLookup ?? []);
            const professorInput = document.getElementById('professor_nome');
            const idProfessorInput = document.getElementById('id_professor');

            if (!professorInput || !idProfessorInput) {
                return;
            }

            const normalize = (value) => (value || '').toString().trim().toLowerCase();

            const preencherIdProfessor = () => {
                const nomeDigitado = normalize(professorInput.value);
                if (!nomeDigitado) {
                    idProfessorInput.value = '';
                    return;
                }

                const professor = professores.find((item) => normalize(item.nome) === nomeDigitado);
                idProfessorInput.value = professor ? (professor.id ?? '') : '';
            };

            professorInput.addEventListener('input', preencherIdProfessor);
            preencherIdProfessor();
        })();
    </script>
</body>
</html>
