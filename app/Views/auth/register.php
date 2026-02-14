@extends('layouts.app')

@section('content')
<style>
    .auth-stage {
        max-width: 450px;
        margin: 0 auto;
        padding-bottom: 100px;
    }

    .auth-card {
        background: rgba(10, 10, 10, 0.6);
        backdrop-filter: blur(20px);
        border: 1px solid var(--border);
        border-radius: 8px;
        padding: 50px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.5);
    }

    .auth-header {
        text-align: center;
        margin-bottom: 40px;
    }

    .auth-header h1 {
        font-size: 2rem;
        font-weight: 900;
        letter-spacing: -1px;
        text-transform: uppercase;
        margin-bottom: 10px;
    }

    .auth-header p {
        color: var(--text-dim);
        font-size: 0.85rem;
        letter-spacing: 1px;
    }

    .form-group {
        margin-bottom: 25px;
    }

    .form-group label {
        display: block;
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--text-dim);
        text-transform: uppercase;
        letter-spacing: 2px;
        margin-bottom: 8px;
    }

    .auth-input {
        width: 100%;
        padding: 15px 20px;
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid var(--border);
        border-radius: 4px;
        color: #fff;
        font-family: inherit;
        transition: all 0.3s;
    }

    .auth-input:focus {
        outline: none;
        border-color: var(--cyan);
        background: rgba(6, 182, 212, 0.05);
    }

    .btn-auth {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, var(--purple), #7c3aed);
        color: #fff;
        border: none;
        border-radius: 4px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 2px;
        cursor: pointer;
        transition: all 0.3s;
        margin-top: 10px;
    }

    .btn-auth:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(139, 92, 246, 0.4);
    }

    .auth-footer {
        margin-top: 30px;
        text-align: center;
        font-size: 0.8rem;
        color: var(--text-dim);
    }

    .auth-footer a {
        color: var(--cyan);
        text-decoration: none;
        font-weight: 700;
    }

    .alert-error {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid #ef4444;
        color: #ef4444;
        padding: 15px;
        border-radius: 4px;
        margin-bottom: 25px;
        font-size: 0.85rem;
        text-align: center;
        font-weight: 600;
    }
</style>

<div class="auth-stage">
    <div class="auth-card">
        <div class="auth-header">
            <h1>New <span>Entity</span></h1>
            <p>ESTABLISHING CORE IDENTITY</p>
        </div>

        @if(isset($error))
        <div class="alert-error">{{ $error }}</div>
        @endif

        <form action="/register" method="POST">
            {!! csrf_field() !!}
            <div class="form-group">
                <label>Entity Username</label>
                <input type="text" name="username" class="auth-input" placeholder="TITAN_PILOT" required>
            </div>
            <div class="form-group">
                <label>Uplink Email</label>
                <input type="email" name="email" class="auth-input" placeholder="nexus@core.id" required>
            </div>
            <div class="form-group">
                <label>Matrix Access Key</label>
                <input type="password" name="password" class="auth-input" placeholder="••••••••" required>
            </div>
            <button type="submit" class="btn-auth">Forge Identity</button>
        </form>

        <div class="auth-footer">
            Already verified? <a href="/login">Re-establish Access</a>
        </div>
    </div>
</div>
@endsection