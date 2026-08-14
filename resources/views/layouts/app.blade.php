<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciador de Finanças</title>

    <link rel="manifest" href="/manifest.json">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16.png">
    <link rel="apple-touch-icon" href="/icons/favicon-180.png">
    <meta name="theme-color" content="#0f172a">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Finanças">
    @auth
    <meta name="vapid-public-key" content="{{ config('webpush.vapid.public_key') }}">
    @endauth

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .navbar { margin-bottom: 2rem; }
        .privacidade-ativa .dado-sensivel {
            filter: blur(6px);
            user-select: none;
        }
    </style>
    <script>
        if (localStorage.getItem('privacidadeAtiva') === '1') {
            document.documentElement.classList.add('privacidade-ativa');
        }
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">Finanças</a>
            @auth
            <button type="button" id="btn-privacidade" class="btn btn-link nav-link text-white me-2 order-lg-2" title="Mostrar/ocultar nomes e valores">
                <span id="icone-privacidade">👁️</span>
            </button>
            @endauth
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('emprestimos.index') }}">Empréstimos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('clientes.index') }}">Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('usuarios.index') }}">Usuários</a>
                    </li>
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @auth
                    <li class="nav-item">
                        <button type="button" id="btn-ativar-notificacoes" class="btn btn-link nav-link">Ativar notificações</button>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('empresa.edit') }}">Minha Empresa </a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link">Sair ({{ Auth::user()->name }})</button>
                        </form>
                    </li>
                    @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Entrar</a>
                    </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function urlBase64ToUint8Array(base64String) {
            const padding = '='.repeat((4 - base64String.length % 4) % 4);
            const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
            const rawData = window.atob(base64);
            return Uint8Array.from([...rawData].map((char) => char.charCodeAt(0)));
        }

        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js');
        }

        const btnPrivacidade = document.getElementById('btn-privacidade');
        if (btnPrivacidade) {
            const iconePrivacidade = document.getElementById('icone-privacidade');

            function atualizarIcone() {
                iconePrivacidade.textContent = document.documentElement.classList.contains('privacidade-ativa') ? '🙈' : '👁️';
            }

            atualizarIcone();

            btnPrivacidade.addEventListener('click', () => {
                const ativo = document.documentElement.classList.toggle('privacidade-ativa');
                localStorage.setItem('privacidadeAtiva', ativo ? '1' : '0');
                atualizarIcone();
            });
        }

        const btnAtivarNotificacoes = document.getElementById('btn-ativar-notificacoes');
        if (btnAtivarNotificacoes) {
            btnAtivarNotificacoes.addEventListener('click', async () => {
                try {
                    const permissao = await Notification.requestPermission();
                    if (permissao !== 'granted') {
                        alert('Permissão de notificação negada.');
                        return;
                    }

                    const vapidPublicKey = document.querySelector('meta[name="vapid-public-key"]').content;
                    const registration = await navigator.serviceWorker.ready;
                    const subscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
                    });

                    await fetch('{{ route('push.subscribe') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                        body: JSON.stringify(subscription),
                    });

                    alert('Notificações ativadas!');
                } catch (erro) {
                    console.error(erro);
                    alert('Não foi possível ativar as notificações.');
                }
            });
        }
    </script>
</body>
</html>
