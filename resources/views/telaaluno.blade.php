<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tela do Aluno</title>
    <style>
        body { margin: 0; font-family: "Segoe UI", sans-serif; background: #f3f7ff; }
        header { background: linear-gradient(45deg, #002168, #3676ff); color: #fff; padding: 18px 24px; }
        main { padding: 24px; }
        .student-nav { display: flex; gap: 10px; margin-bottom: 18px; flex-wrap: wrap; }
        .student-link { text-decoration: none; padding: 10px 14px; border-radius: 8px; background: #e7eefc; color: #0b2447; font-weight: 600; }
        .student-link.active { background: #0b2447; color: #fff; }
        .card { background: #fff; border-radius: 12px; padding: 22px; box-shadow: 0 8px 22px rgba(0, 0, 0, 0.08); max-width: 900px; }
    </style>
</head>
<body>
    <header>
         <a href="{{ route('telaaluno') }}"><img src="{{ asset('img/jardim-logotipo122.png') }}" alt="Imagem" width="120px" height="70px" class="logo"></a>
        <h3>Ambiente do Aluno</h3>
    </header>
    <main>
        <nav class="student-nav">
            <a class="student-link active" href="{{ route('telaaluno.noticias') }}">Noticias</a>
            <a class="student-link" href="{{ route('telaaluno.chat') }}">Chat</a>
            <a class="student-link" href="{{ route('telaaluno.caderneta') }}">Caderneta</a>
            <a class="student-link" href="{{ route('telaaluno.perfil') }}">Perfil</a>
            <a href="{{ route('logout') }}" class="student-link">
                <i class="fa-solid fa-right-from-bracket"></i> Sair
            </a>
        </nav>
        <section class="card">
            <h4>Area do Aluno</h4>
            <p>Usa o menu acima para navegar entre Noticias, Chat, Caderneta e Perfil.</p>
        </section>
    </main>
</body>
</html>


