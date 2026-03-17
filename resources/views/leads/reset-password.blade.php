@extends('layouts.app')

@section('title', 'Lead Portal Reset Password')

@section('content')
<div class="container" style="max-width:480px;margin:40px auto;">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Reset Password</h1>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('lead.password.reset') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ $email }}">

                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           required
                           autocomplete="new-password">
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password"
                           name="password_confirmation"
                           class="form-control"
                           required
                           autocomplete="new-password">
                </div>

                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:10px;">
                    Reset Password
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

