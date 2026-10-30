<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard - Chikumi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background: #FFF9F5;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card {
            background: #FFF3E6;
            padding: 40px 48px;
            border-radius: 20px;
            text-align: center;
            box-shadow: 0 8px 30px rgba(232, 117, 0, .12);
            max-width: 380px;
            width: 100%;
        }

        .role {
            display: inline-block;
            padding: 4px 14px;
            margin-bottom: 10px;
            border-radius: 999px;
            background: #FF8000;
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .5px;
        }

        h1 { font-size: 20px; color: #302C29; margin-bottom: 4px; }

        p { color: #81766E; font-size: 13px; margin-bottom: 28px; }

        form { display: inline; }

        button {
            font-family: inherit;
            padding: 12px 24px;
            border: none;
            border-radius: 12px;
            background: #FF8000;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background .2s;
            width: 100%;
        }

        button:hover { background: #E87500; }
    </style>
</head>

<body>
    <div class="card">
        <span class="role">{{ Auth::user()->role }}</span>
        <h1>Halaman sementara</h1>
        <p>Halo, {{ Auth::user()->name }}</p>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Keluar</button>
        </form>
    </div>
</body>

</html>
