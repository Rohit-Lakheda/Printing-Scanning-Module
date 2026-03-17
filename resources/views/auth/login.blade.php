@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="card" style="max-width: 400px; margin: 100px auto;">
    <div class="card-header">
        <h1 class="card-title" style="text-align: center;">Login</h1>
    </div>

    <form action="{{ route('login') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            @error('email')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" required>
            @error('password')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
        </div>
    </form>
</div>
@endsection
