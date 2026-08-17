<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel do Dono — Gerenciador de Finanças</title>

    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/icons/favicon-16.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .app-navbar {
            background: linear-gradient(135deg, #0f172a, #1d4ed8);
            margin-bottom: 2rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        .app-navbar .navbar-brand { font-weight: 700; letter-spacing: 0.02em; }
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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark app-navbar">
        <div class="container-fluid container-md">
            <a class="navbar-brand mb-0 d-flex align-items-center gap-2" href="{{ route('admin.empresas.index') }}">
                <span>🛠️</span> Painel do Dono
            </a>

            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.empresas.*') ? 'active' : '' }}" href="{{ route('admin.empresas.index') }}">Empresas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}" href="{{ route('admin.logs.index') }}">Logs</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-link nav-link">Sair ({{ Auth::user()->name }})</button>
                        </form>
                    </li>
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
</body>
</html>
