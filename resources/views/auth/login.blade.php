@extends('layouts.app')

@section('content')
<div class="auth-wrapper">
    <div class="auth-form-container">
        <div class="auth-form-header">
            <h1>Welcome Back</h1>
            <p>Please log in to continue</p>
        </div>
        <div class="auth-form-body">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group mb-4">
                    <label for="email" class="form-label">Email Address</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    @error('password')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                </div>

                <div class="form-group mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label class="form-check-label" for="remember">
                            Remember Me
                        </label>
                    </div>
                </div>

                <div class="form-group mb-0 text-center">
                    <button type="submit" class="btn btn-login">Login</button>
                    @if (Route::has('password.request'))
                        <div class="mt-3">
                            <a class="btn btn-link" href="{{ route('password.request') }}">
                                Forgot Your Password?
                            </a>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    body {
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #6e8efb, #a777e3);
        overflow: hidden;
        font-family: 'Poppins', sans-serif;
    }

    .auth-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background: rgba(0, 0, 0, 0.5);
    }

    .auth-form-container {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 15px;
        backdrop-filter: blur(10px);
        padding: 2rem;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
        position: relative;
        animation: pop-in 0.5s ease-out;
    }

    @keyframes pop-in {
        0% {
            transform: scale(0.8);
            opacity: 0;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .auth-form-header {
        text-align: center;
        margin-bottom: 1rem;
    }

    .auth-form-header h1 {
        font-size: 2.5rem;
        color: #fff;
        margin-bottom: 0.5rem;
        font-weight: 700;
    }

    .auth-form-header p {
        color: #eee;
        font-size: 1.1rem;
    }

    .form-label {
        font-weight: 600;
        color: #fff;
    }

    .form-control {
        border-radius: 10px;
        padding: 0.75rem 1.25rem;
        border: 1px solid rgba(255, 255, 255, 0.3);
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        transition: border-color 0.3s, background-color 0.3s;
    }

    .form-control:focus {
        border-color: #a777e3;
        background-color: rgba(255, 255, 255, 0.3);
        outline: none;
        box-shadow: 0 0 0 0.2rem rgba(167, 119, 227, 0.25);
    }

    .form-control::placeholder {
        color: #ddd;
    }

    .btn-login {
        background: #6e8efb;
        border: none;
        border-radius: 25px;
        padding: 0.75rem 1.5rem;
        color: #fff;
        font-weight: 600;
        font-size: 1rem;
        transition: background 0.3s, transform 0.3s;
        text-transform: uppercase;
    }

    .btn-login:hover {
        background: #5a6abf;
        transform: translateY(-2px);
    }

    .btn-login:active {
        background: #4a5b9b;
        transform: translateY(0);
    }

    .form-check-label {
        font-size: 0.9rem;
        color: #fff;
    }

    .btn-link {
        color: #a777e3;
        font-size: 0.9rem;
        text-decoration: none;
        transition: color 0.3s;
    }

    .btn-link:hover {
        color: #6e8efb;
    }
</style>
@endpush
