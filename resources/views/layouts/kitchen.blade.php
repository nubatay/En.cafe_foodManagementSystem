<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kitchen Display System - En.cafe</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@700;800&family=Inter:wght@400;500;600&display=swap');

        :root {
            --primary-red: #DC2626;
            --accent-orange: #F59E0B;
            --success-green: #16A34A;
            --bg-warm: #FFF7ED;
            --text-dark: #1F2937;
        }

        body { 
            background: var(--bg-warm); 
            font-family: 'Inter', sans-serif; 
            color: var(--text-dark);
        }

        /* Top Navigation Style */
        .kitchen-header {
            background: #FFFFFF;
            padding: 1rem 2rem;
            border-bottom: 2px solid #F3E8FF;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.02);
        }

        .brand-logo {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-red);
            text-decoration: none;
        }

        .logout-btn {
            background-color: var(--primary-red);
            color: white;
            font-weight: 600;
            padding: 0.5rem 1.2rem;
            border-radius: 8px;
            transition: all 0.2s;
            border: none;
        }

        .logout-btn:hover {
            background-color: #B91C1C;
            color: white;
            box-shadow: 0 4px 12px rgba(220, 38, 38, 0.2);
        }

        .main-container {
            padding: 2rem;
        }
    </style>
</head>
<body>

<header class="kitchen-header">
    <a href="#" class="brand-logo d-flex align-items-center">
        KITCHEN
    </a>
    
    <div class="d-flex align-items-center gap-4">
        <div class="text-end d-none d-md-block">
            <small class="text-muted d-block" style="font-size: 0.7rem;">STATION CHIEF</small>
            <span class="fw-bold">{{ auth()->user()->name }}</span>
        </div>
        
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout-btn">
                <i class="bi bi-box-arrow-left me-2"></i> Logout
            </button>
        </form>
    </div>
</header>

<main class="main-container">
    @yield('content')
</main>

</body>
</html>
