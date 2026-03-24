<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aluno - Caderneta</title>
    <style>
        body { margin: 0; font-family: "Segoe UI", sans-serif; background: #f3f7ff; }
        header { background: linear-gradient(45deg, #002168, #3676ff); color: #fff; padding: 18px 24px; }
        main { padding: 24px; }
        .student-nav { display: flex; gap: 10px; margin-bottom: 18px; flex-wrap: wrap; }
        .student-link { text-decoration: none; padding: 10px 14px; border-radius: 8px; background: #e7eefc; color: #0b2447; font-weight: 600; }
        .student-link.active { background: #0b2447; color: #fff; }
        .card { background: #fff; border-radius: 12px; padding: 22px; box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08); max-width: 900px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        .table th, .table td { border-bottom: 1px solid #dbe3f1; padding: 10px; text-align: left; }
    </style>
</head>
<body>
    <header>
         <a href="{{ route('telaaluno') }}"><img src="{{ asset('img/jardim-logotipo122.png') }}" alt="Imagem" width="120px" height="70px" class="logo"></a>
        <h3>Ambiente do Aluno - Caderneta</h3>
</header>
    <main>
        <nav class="student-nav">
            <a class="student-link" href="{{ route('telaaluno.noticias') }}">Noticias</a>
            <a class="student-link" href="{{ route('telaaluno.chat') }}">Chat</a>
            <a class="student-link active" href="{{ route('telaaluno.caderneta') }}">Caderneta</a>
            <a class="student-link" href="{{ route('telaaluno.perfil') }}">Perfil</a>
            <a href="{{ route('logout') }}" class="student-link">
                <i class="fa-solid fa-right-from-bracket"></i> Sair
            </a>
        </nav>
        
        <section class="card">
            <h4>Caderneta</h4>
            <table class="table">
                <thead>
                    <tr><th>Aluno</th><th>Turma</th><th>Desempenho</th><th>Descricao</th></tr>
                </thead>
                <tbody>
                    @if ($aluno)
                        <tr>
                            <td>{{ data_get($aluno, 'nome', $nomeAluno ?: '-') }}</td>
                            <td>{{ data_get($aluno, 'turma', '-') }}</td>
                            <td>{{ $valorDesempenho ?? '-' }}</td>
                            <td>{{ $valorDescricao ?? '-' }}</td>
                        </tr>
                    @else
                        <tr>
                            <td colspan="4">Sem dados de caderneta para o utilizador {{ $meuEmail ?: '-' }}. Vincule um Perfil do Aluno no Acesso Alunos.</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>

