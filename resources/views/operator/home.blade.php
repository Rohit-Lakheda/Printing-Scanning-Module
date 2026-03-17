@extends('layouts.app')

@section('title', 'Operator Home')

@section('content')
<div class="card" style="max-width: 800px; margin: 50px auto; text-align: center;">
    <div class="card-header">
        <h1 class="card-title">Welcome to Badge System</h1>
    </div>

    <p style="margin-top: 20px; color: #6b7280; font-size: 18px;">
        Please select an option to continue
    </p>

    <div style="margin-top: 50px; display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 30px; padding: 20px;">
        <a href="{{ route('operator.badge.menu') }}" class="btn btn-primary" style="padding: 40px 30px; font-size: 20px; text-decoration: none; display: block;">
            <div style="font-weight: bold; margin-bottom: 10px;">Printing</div>
            <div style="font-size: 14px; opacity: 0.9;">Print badges for users</div>
        </a>
        
        <a href="{{ route('operator.scanning.select-location') }}" class="btn btn-secondary" style="padding: 40px 30px; font-size: 20px; text-decoration: none; display: block;">
            <div style="font-weight: bold; margin-bottom: 10px;">Scanning</div>
            <div style="font-size: 14px; opacity: 0.9;">Scan and verify user access</div>
        </a>
        
        <a href="{{ route('operator.registration.create') }}" class="btn" style="background-color: #10b981; color: white; padding: 40px 30px; font-size: 20px; text-decoration: none; display: block;">
            <div style="font-weight: bold; margin-bottom: 10px;">Onsite Registration</div>
            <div style="font-size: 14px; opacity: 0.9;">Register and print badge</div>
        </a>
    </div>
</div>
@endsection
