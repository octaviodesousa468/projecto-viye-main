
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat+Alternates:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap');
    </style>

    <title>Projecto Viye</title>
</head>
<body>
    
    <div class="painel">
        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <div class="login">
                <img src="img/jardim-logotipo 2.0.png" alt="Imagem" width="300px" height="250px" class="logo">
              @if ($errors->any())
                <p style="color:#b91c1c; margin-bottom:10px;">{{ $errors->first() }}</p>
              @endif
              @if (session('session_expired'))
                <p style="color:#b45309; margin-bottom:10px;">{{ session('session_expired') }}</p>
              @endif
              <input type="email" name="email" value="{{ old('email') }}" placeholder="Email de usuario" required> 
              <input type="password" name="password" placeholder="Senha">
            <button type="submit"> Entrar</button>
            <p>Nao tenho acesso<a href="">Solicitar Acesso.</a></p>
            </div>
        </form>
    </div>
    
    <script src="{{ asset('js/welcome-gradient.js') }}"></script>
</body>
</html>
