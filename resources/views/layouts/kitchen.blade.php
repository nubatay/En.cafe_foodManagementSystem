<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kitchen - En.cafe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: #f5f7fb;
        }

        .sidebar {
            width: 220px;
            min-height: 100vh;
            background: #111827;
        }

        .sidebar .nav-link {
            color: #fff;
        }

        .sidebar .nav-link.active {
            background: #1f2937;
        }

        .content {
            flex: 1;
            padding: 20px;
        }

        .table-card {
            border-radius: 18px;
            border: 0;
            box-shadow: 0 10px 24px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>

<div class="d-flex">

    <div class="sidebar p-3 text-white">
        <h4>Kitchen</h4>

        <a href="{{ route('kitchen.orders') }}" class="nav-link active">
            Orders
        </a>

        <hr>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="btn btn-danger w-100">Logout</button>
        </form>
    </div>

    <div class="content">
        @yield('content')
    </div>

</div>

</body>
</html>