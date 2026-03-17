@extends('layouts.app')

@section('title', 'Enter Receipt Number')

@section('content')
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <div class="card-header">
        <h1 class="card-title">Enter Receipt Number</h1>
    </div>

    <div style="padding: 20px;">
        <p style="margin-bottom: 20px; color: #6b7280;">
            Please enter receipt number to complete registration.<br><br>
            RegID: <strong>{{ $registrationData['RegID'] }}</strong><br>
            Name: <strong>{{ $registrationData['Name'] }}</strong><br>
            Category: <strong>{{ $registrationData['Category'] }}</strong>
        </p>

        <form action="{{ route('operator.registration.store-receipt') }}" method="POST">
            @csrf

            <div class="form-group">
                <label class="form-label">Receipt Number <span style="color: #ef4444;">*</span></label>
                <input type="text" 
                       name="ReceiptNumber" 
                       class="form-control" 
                       required 
                       autofocus
                       placeholder="Enter receipt number"
                       value="{{ old('ReceiptNumber') }}">
                @error('ReceiptNumber')
                    <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-top: 30px; display: flex; gap: 10px;">
                <button type="submit" class="btn btn-primary">Complete Registration & Print</button>
                <a href="{{ route('operator.registration.cancel') }}" 
                   class="btn btn-secondary" 
                   onclick="event.preventDefault(); if(confirm('Are you sure you want to cancel this registration? All entered data will be lost.')) { document.getElementById('cancel-form').submit(); }">Cancel Registration</a>
            </div>
        </form>

        <form id="cancel-form" action="{{ route('operator.registration.cancel') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-top: 20px; max-width: 600px; margin-left: auto; margin-right: auto;">
        {{ session('success') }}
    </div>
@endif
@endsection
