@extends('layouts.app')

@section('title', 'Select Location')

@section('content')
<div class="card" style="max-width: 700px; margin: 50px auto;">
    <div class="card-header">
        <h1 class="card-title">Select Location for Scanning</h1>
    </div>

    <form method="POST" action="{{ route('operator.scanning.store-location') }}" style="padding: 20px;">
        @csrf
        
        <div style="margin-bottom: 20px;">
            <label for="location_id" style="display: block; margin-bottom: 8px; font-weight: bold;">Location:</label>
            <select name="location_id" id="location_id" required class="form-control" style="width: 100%; padding: 10px; font-size: 16px;">
                <option value="">-- Select a Location --</option>
                @foreach($locations as $location)
                    <option value="{{ $location->id }}">{{ $location->name }}</option>
                @endforeach
            </select>
            @error('location_id')
                <div style="color: red; margin-top: 5px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: flex; gap: 10px; margin-top: 30px;">
            <button type="submit" class="btn btn-primary" style="flex: 1; padding: 12px;">Continue to Scanning</button>
            <a href="{{ route('operator.home') }}" class="btn btn-secondary" style="padding: 12px 20px;">Back</a>
        </div>
    </form>
</div>
@endsection
