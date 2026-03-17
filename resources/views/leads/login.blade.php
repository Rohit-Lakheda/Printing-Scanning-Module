@extends('layouts.app')

@section('title', 'Lead Portal Login')

@section('content')
<div class="container" style="max-width:480px;margin:40px auto;">
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Lead Portal Login</h1>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('lead.login') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text"
                           name="username"
                           class="form-control"
                           value="{{ old('username') }}"
                           required
                           autocomplete="off">
                </div>
                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password"
                           name="password"
                           class="form-control"
                           required
                           autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;margin-top:10px;">
                    Login
                </button>
                <div style="margin-top: 12px; text-align: center;">
                    <a href="{{ route('lead.password.forgot.form') }}" style="font-size: 13px; color: #1d4ed8; text-decoration: none;">
                        Forgot Password?
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/lead-sw.js').catch(function () {});
    });
}
</script>
@endpush

