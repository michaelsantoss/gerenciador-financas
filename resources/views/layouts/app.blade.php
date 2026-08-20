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
        .privacidade-ativa .dado-sensivel {
            filter: blur(6px);
            user-select: none;
        }

        /* Ações de parcela (pagar/desfazer): lado a lado no desktop, empilhado no mobile */
        .parcela-acao-form .parcela-acao-valor { width: 110px; }

        /* Linha de tabela clicável (navega pro detalhe, exceto botões/links/forms internos) */
        tr.linha-clicavel { cursor: pointer; }
        tr.linha-clicavel:hover { background-color: rgba(13, 110, 253, 0.06); }

        /* Botão circular de voltar, usado nos cabeçalhos das telas de detalhe/formulário */
        .btn-voltar {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            flex-shrink: 0;
            border-radius: 50%;
            background: #fff;
            border: 1px solid #dee2e6;
            color: #0f172a;
        }
        .btn-voltar:hover { background: #e9ecef; color: #0f172a; }

        /* Menu superior */
        .app-navbar {
            background: linear-gradient(135deg, #0f172a, #1d4ed8);
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        .app-navbar .navbar-brand {
            font-weight: 700;
            letter-spacing: 0.02em;
        }
        .app-navbar-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            border: none;
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            font-size: 1.1rem;
            padding: 0;
        }
        .app-navbar-icon-btn:hover { background: rgba(255, 255, 255, 0.18); }
        .app-navbar .navbar-nav .nav-link {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            margin: 0.1rem 0.15rem;
        }
        .app-navbar .navbar-nav .nav-link:hover { background-color: rgba(255, 255, 255, 0.08); }
        .app-navbar .navbar-nav .nav-link.active {
            background-color: rgba(255, 255, 255, 0.16);
            font-weight: 600;
        }
        .app-navbar .dropdown-menu { border: none; box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15); }

        @media (max-width: 576px) {
            /* Alvo de toque maior em botões pequenos, sem afetar o desktop */
            .btn-sm {
                padding: 0.4rem 0.75rem;
                font-size: 0.9rem;
            }

            .parcela-acao-form {
                flex-direction: column;
                align-items: stretch !important;
            }
            .parcela-acao-form .parcela-acao-valor {
                width: 100%;
            }
        }
    </style>
    <script>
        if (localStorage.getItem('privacidadeAtiva') === '1') {
            document.documentElement.classList.add('privacidade-ativa');
        }
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark app-navbar">
        <div class="container-fluid container-md">
            <a class="navbar-brand mb-0 d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <span>💰</span> Finanças
            </a>

            <div class="d-flex align-items-center gap-1">
                @auth
                <button type="button" id="btn-privacidade" class="app-navbar-icon-btn" title="Mostrar/ocultar nomes e valores">
                    <span id="icone-privacidade">👀</span>
                </button>
                <button type="button" id="btn-ativar-notificacoes" class="app-navbar-icon-btn" title="Ativar notificações">
                    🔔
                </button>
                @endauth
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('emprestimos.*') ? 'active' : '' }}" href="{{ route('emprestimos.index') }}">Empréstimos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}" href="{{ route('clientes.index') }}">Clientes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">Usuários</a>
                    </li>
                    @endauth
                </ul>
                <ul class="navbar-nav">
                    @auth
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle {{ request()->routeIs('empresa.edit') ? 'active' : '' }}" href="#" role="button" data-bs-toggle="dropdown">
                            {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('empresa.edit') }}">Minha Empresa</a></li>
                            <li><a class="dropdown-item {{ request()->routeIs('atividades.index') ? 'active' : '' }}" href="{{ route('atividades.index') }}">Log de Atividades</a></li>
                            <li><a class="dropdown-item" href="{{ route('tutorial') }}" target="_blank" rel="noopener">📘 Tutorial</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Sair</button>
                                </form>
                            </li>
                        </ul>
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

    <div class="container-fluid container-md">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                @foreach($errors->all() as $error)
                    {{ $error }}<br>
                @endforeach
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
                iconePrivacidade.textContent = document.documentElement.classList.contains('privacidade-ativa') ? '🙈' : '👀';
            }

            atualizarIcone();

            btnPrivacidade.addEventListener('click', () => {
                const ativo = document.documentElement.classList.toggle('privacidade-ativa');
                localStorage.setItem('privacidadeAtiva', ativo ? '1' : '0');
                atualizarIcone();
            });
        }

        document.querySelectorAll('tr.linha-clicavel').forEach((linha) => {
            linha.addEventListener('click', (event) => {
                if (event.target.closest('a, button, form, input, select, textarea')) {
                    return;
                }
                window.location.href = linha.dataset.href;
            });
        });

        const btnAtivarNotificacoes = document.getElementById('btn-ativar-notificacoes');
        if (btnAtivarNotificacoes && 'serviceWorker' in navigator) {
            async function atualizarEstadoNotificacoes() {
                try {
                    const registration = await navigator.serviceWorker.ready;
                    const subscription = await registration.pushManager.getSubscription();
                    const ativo = !!subscription;
                    btnAtivarNotificacoes.textContent = ativo ? '🔔' : '🔕';
                    btnAtivarNotificacoes.title = ativo ? 'Desativar notificações' : 'Ativar notificações';
                    btnAtivarNotificacoes.dataset.ativo = ativo ? '1' : '0';
                } catch (erro) {
                    console.error(erro);
                }
            }

            atualizarEstadoNotificacoes();

            btnAtivarNotificacoes.addEventListener('click', async () => {
                try {
                    if (btnAtivarNotificacoes.dataset.ativo === '1') {
                        const registration = await navigator.serviceWorker.ready;
                        const subscription = await registration.pushManager.getSubscription();

                        if (subscription) {
                            const endpoint = subscription.endpoint;
                            await subscription.unsubscribe();
                            await fetch('{{ route('push.unsubscribe') }}', {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                },
                                body: JSON.stringify({ endpoint }),
                            });
                        }

                        alert('Notificações desativadas.');
                    } else {
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
                    }
                } catch (erro) {
                    console.error(erro);
                    alert('Não foi possível atualizar as notificações.');
                }

                atualizarEstadoNotificacoes();
            });
        }
    </script>
</body>
</html>
