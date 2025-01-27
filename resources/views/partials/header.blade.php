<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Head content -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            font-family: 'Nunito', sans-serif;
            position: relative;
            background: linear-gradient(to bottom, #e0f7fa, #80deea);
        }

        footer {
            background-color: #4db6ac;
            color: white;
            text-align: center;
            padding: 20px;
            margin-top: auto;
            font-size: 16px;
            border-top: 4px solid #004d40;
        }

        header {
            background: linear-gradient(135deg, #ffeb3b, #ff9800);
            color: #fff;
            text-align: center;
            padding: 40px 20px;
            position: relative;
            border-bottom: 8px solid #ff9800;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        header img {
            max-height: 100px;
            margin-bottom: 15px;
            border-radius: 50%;
            border: 5px solid #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        header h1 {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        header p {
            font-size: 20px;
            margin-top: 0;
            margin-bottom: 20px;
        }

        #app {
            flex: 1;
            padding: 20px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        nav {
            background-color: #ffffff;
            border-bottom: 2px solid #ddd;
            padding: 10px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        nav a {
            color: #00796b;
            font-weight: bold;
        }

        nav a:hover {
            color: #004d40;
            text-decoration: underline;
        }

        .header-decorative {
            background: linear-gradient(90deg, #ffeb3b, #ff9800);
            height: 10px;
            margin-top: 20px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <img src="{{ asset('images/oag_logo.png') }}" alt="Office Logo">
            <h1>Office of the Attorney General</h1>
            <p class="header-description">Ensuring Justice and Legal Integrity</p>
            <div class="header-decorative"></div>
        </div>
    </header>

    <div id="app">
        <!-- Main content goes here -->
    </div>

    

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
