<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>En.cafe</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            background: #f8fafc;
            color: #0f172a;
        }

        .app-shell {
            min-height: 100vh;
        }

        .page-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 20px 48px;
        }

        .glass-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        .hero-card {
            background: linear-gradient(135deg, #111827, #374151);
            color: white;
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(17, 24, 39, 0.18);
        }

        .section-title {
            font-size: 1.15rem;
            font-weight: 700;
            margin-bottom: 1rem;
        }

        .soft-text {
            color: #6b7280;
        }

        .btn {
            border-radius: 12px;
            padding: 10px 18px;
            font-weight: 600;
        }

        .btn-sm {
            border-radius: 10px;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            font-weight: 700;
        }

        .badge {
            padding: 8px 12px;
            border-radius: 999px;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 10px 14px;
        }

        .card-clean {
            border: 0;
            border-radius: 22px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }
    </style>
</head>
<body>
    <div class="app-shell">
        @include('layouts.navigation')

        <main class="page-wrap">
            @if(session('success'))
                <div class="alert alert-success border-0 shadow-sm rounded-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger border-0 shadow-sm rounded-4">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-warning border-0 shadow-sm rounded-4">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>