<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Editar Registro</title>

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
        .input-group input,
        .input-group select {
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
        <h3>Editar {{ ucfirst($tipo) }}</h3>
    </header>

    <section class="card">
        <form method="POST" action="{{ route('database.update', ['tipo' => $tipo, 'id' => $id, 'q' => request()->query('q', '')]) }}" class="form-card">
            @csrf
            @method('PUT')

            <div class="form-grid">
                @if ($tipo === 'aluno')
                    <div class="input-group">
                        <label>Nome</label>
                        <input type="text" name="nome" value="{{ old('nome', data_get($registro, 'nome')) }}" required>
                    </div>
                    <div class="input-group">
                        <label>Data de nascimento</label>
                        <input type="date" name="data_nascimento" value="{{ old('data_nascimento', data_get($registro, 'data_nascimento')) }}" required>
                    </div>
                    <div class="input-group">
                        <label>Sexo</label>
                        <select name="sexo" required>
                            <option value="Masculino" {{ old('sexo', data_get($registro, 'sexo')) === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="Feminino" {{ old('sexo', data_get($registro, 'sexo')) === 'Feminino' ? 'selected' : '' }}>Feminino</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>BI</label>
                        <input type="text" name="bi" value="{{ old('bi', data_get($registro, 'bi')) }}" required>
                    </div>
                    <div class="input-group">
                        <label>Nacionalidade</label>
                        <input type="text" name="nacionalidade" value="{{ old('nacionalidade', data_get($registro, 'nacionalidade')) }}" required>
                    </div>
                    <div class="input-group">
                        <label>Encarregados</label>
                        <input type="text" name="encarregados" value="{{ old('encarregados', data_get($registro, 'encarregados')) }}" required>
                    </div>
                    <div class="input-group">
                        <label>Turma</label>
                        <input type="text" name="turma" value="{{ old('turma', data_get($registro, 'turma')) }}" placeholder="Turma (opcional)">
                    </div>
                    <div class="input-group">
                        <label>Desempenho</label>
                        <input type="number" name="desempenho" value="{{ old('desempenho', data_get($registro, 'desempenho')) }}" min="0" max="20" step="0.1" placeholder="0 a 20 (opcional)">
                    </div>
                    <div class="input-group">
                        <label>Descricao</label>
                        <input type="text" name="descricao" value="{{ old('descricao', data_get($registro, 'descricao')) }}" maxlength="255" placeholder="Descricao (opcional)">
                    </div>
                @elseif ($tipo === 'professor')
                    <div class="input-group">
                        <label>Nome</label>
                        <input type="text" name="nome" value="{{ old('nome', data_get($registro, 'nome')) }}" required>
                    </div>
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', data_get($registro, 'email')) }}" required>
                    </div>
                    <div class="input-group">
                        <label>Telefone</label>
                        <input type="text" name="telefone" value="{{ old('telefone', data_get($registro, 'telefone')) }}" required>
                    </div>
                    <div class="input-group">
                        <label>Disciplina</label>
                        <input type="text" name="disciplina" value="{{ old('disciplina', data_get($registro, 'disciplina')) }}" required>
                    </div>
                    <div class="input-group">
                        <label>Nova password (opcional)</label>
                        <input type="password" name="password" placeholder="Deixe vazio para manter">
                    </div>
                @elseif ($tipo === 'funcionario')
                    <div class="input-group">
                        <label>Nome</label>
                        <input type="text" name="nome" value="{{ old('nome', data_get($registro, 'nome')) }}" required>
                    </div>
                    <div class="input-group">
                        <label>Data de nascimento</label>
                        <input type="date" name="data_nascimento" value="{{ old('data_nascimento', data_get($registro, 'data_nascimento')) }}" required>
                    </div>
                    <div class="input-group">
                        <label>Sexo</label>
                        <select name="sexo" required>
                            <option value="Masculino" {{ old('sexo', data_get($registro, 'sexo')) === 'Masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="Femenino" {{ old('sexo', data_get($registro, 'sexo')) === 'Femenino' ? 'selected' : '' }}>Femenino</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>BI/Passaporte</label>
                        <input type="text" name="bi_passaporte" value="{{ old('bi_passaporte', data_get($registro, 'bi_passaporte')) }}" required>
                    </div>
                    <div class="input-group">
                        <label>Nacionalidade</label>
                        <input type="text" name="nacionalidade" value="{{ old('nacionalidade', data_get($registro, 'nacionalidade')) }}" maxlength="20" required>
                    </div>
                    <div class="input-group">
                        <label>Contacto</label>
                        <input type="number" name="contacto" value="{{ old('contacto', data_get($registro, 'contacto')) }}" min="0" max="255" required>
                    </div>
                    <div class="input-group">
                        <label>Email</label>
                        <input type="email" name="email" value="{{ old('email', data_get($registro, 'email')) }}" maxlength="35" required>
                    </div>
                    <div class="input-group">
                        <label>Formacao</label>
                        <input type="text" name="formacao" value="{{ old('formacao', data_get($registro, 'formacao')) }}" maxlength="90" required>
                    </div>
                    <div class="input-group">
                        <label>Nivel academico</label>
                        <input type="text" name="nivel_academico" value="{{ old('nivel_academico', data_get($registro, 'nivel_academico')) }}" maxlength="25" required>
                    </div>
                    <div class="input-group">
                        <label>Endereco</label>
                        <input type="text" name="endereco" value="{{ old('endereco', data_get($registro, 'endereco')) }}" maxlength="50" required>
                    </div>
                    <div class="input-group">
                        <label>Funcao</label>
                        <input type="text" name="funcao" value="{{ old('funcao', data_get($registro, 'funcao')) }}" maxlength="50" required>
                    </div>
                    <div class="input-group">
                        <label>Departamento</label>
                        <input type="text" name="departamento" value="{{ old('departamento', data_get($registro, 'departamento')) }}" maxlength="40" required>
                    </div>
                @endif
            </div>

            <div class="form-actions">
                <button type="submit" class="btn edit">Guardar alteracoes</button>
                <a href="{{ route('database', ['q' => request()->query('q', '')]) }}" class="btn">Voltar</a>
            </div>
        </form>
    </section>
</main>
</body>
</html>
