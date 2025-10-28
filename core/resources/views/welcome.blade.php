<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AI Business Assistant | {{ config('app.name') }}</title>

    @vite('resources/css/app.css')

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, #0f172a, #1e3a8a);
            color: white;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            background-image: url('https://images.unsplash.com/photo-1521791136064-7986c2920216?auto=format&fit=crop&w=1500&q=80');
            background-size: cover;
            background-position: center;
        }

        .overlay {
            background-color: rgba(0, 0, 0, 0.55);
            position: absolute;
            inset: 0;
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 10;
            text-align: center;
            max-width: 650px;
            padding: 2rem;
            border-radius: 1rem;
            backdrop-filter: blur(10px);
            background-color: rgba(255, 255, 255, 0.1);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        }

        .logo {
            width: 100px;
            margin-bottom: 1rem;
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }

        p {
            line-height: 1.6;
            opacity: 0.9;
        }

        .btn-group {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        /* Get Started Button */
        .btn-primary {
            background-color: #74bc2d;
        }

        .btn-primary:hover {
            background-color: #5da322;
        }

        /* Login Button */
        .btn-outline {
            border: 2px solid #74bc2d;
            color: #74bc2d;
            background-color: transparent;
        }

        .btn-outline:hover {
            background-color: #74bc2d;
            color: white;
        }
    </style>
</head>
<body>
    <div class="overlay"></div>

    <div class="container">
        {{-- Logo --}}
        <img src="{{ asset('logo.png') }}" alt="App Logo" class="logo">

        {{-- Headline --}}
        <h1>Empower Your Business with Smart AI Solutions</h1>

        {{-- Description --}}
        <p>
            Our intelligent assistant helps you automate calls, improve customer experience, 
            and manage your business effortlessly. Sign up now and let AI work for you.
        </p>

        {{-- Buttons --}}
        <div class="btn-group">
            <a href="{{ route('register') }}" class="btn btn-primary">Get Started</a>
            <a href="{{ route('login') }}" class="btn btn-outline">Login</a>
        </div>
    </div>
</body>
</html>
