<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>En.cafe Customer</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
        }

        .customer-wrap {
            max-width: 1280px;
            margin: 0 auto;
            padding: 32px 20px 48px;
        }

        .card-clean {
            border: 0;
            border-radius: 22px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        }

        .hero-card {
            background: linear-gradient(135deg, #111827, #374151);
            color: white;
            border-radius: 28px;
            box-shadow: 0 18px 40px rgba(17, 24, 39, 0.18);
        }

        .soft-text {
            color: #6b7280;
        }

        .btn {
            border-radius: 12px;
            font-weight: 600;
        }

        .form-control,
        .form-select {
            border-radius: 12px;
            padding: 10px 14px;
        }
    </style>
</head>
<body>

<div class="customer-wrap">

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">

        {{-- 🟦 TABLE BADGE --}}
        <div>
            @if(session('table_number'))
                <span class="badge bg-primary fs-6 px-3 py-2">
                    Table {{ session('table_number') }}
                </span>
            @else
                <span class="badge bg-secondary fs-6 px-3 py-2">
                    No Table Selected
                </span>
            @endif
        </div>

        {{-- 🔴 LOGOUT --}}
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-outline-danger btn-sm">
                ⏻ Logout
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 rounded-4 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 rounded-4 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-warning border-0 rounded-4 shadow-sm">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @yield('content')

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>