@extends('layouts.app')

@section('title', 'Lead Portal Forgot Password')

@section('content')
<div class="container" style="max-width:480px;margin:40px auto;">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Forgot Password</h1>
        </div>
        <div class="card-body">
            <p style="font-size:13px;color:#6b7280;margin-bottom:14px;">
                Enter your username or registered email. If account exists, reset link will be sent to your email.
            </p>

            <form method="POST" action="{{ route('lead.password.forgot.send') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Username or Email</label>
                    <input type="text"
                           name="identifier"
                           class="form-control"
                           value="{{ old('identifier') }}"
                           required
                           autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:10px;">
                    Send Reset Link
                </button>
                <div style="margin-top: 12px; text-align: center;">
                    <a href="{{ route('lead.login.form') }}" style="font-size: 13px; color: #1d4ed8; text-decoration: none;">
                        Back to Login
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

