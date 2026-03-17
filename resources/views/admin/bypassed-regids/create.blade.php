@extends('layouts.app')

@section('title', 'Create Bypassed RegID')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Create Bypassed RegID</h1>
    </div>

    <form action="{{ route('admin.bypassed-regids.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">Registration ID (RegID) *</label>
            <input type="text" name="regid" class="form-control" value="{{ old('regid') }}" required placeholder="Enter RegID to bypass">
            @error('regid')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
            <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">The RegID must exist in the user database.</p>
        </div>

        <div class="form-group">
            <label class="form-label">Reason *</label>
            <textarea name="reason" class="form-control" rows="3" placeholder="Reason for bypassing this RegID (required)" required>{{ old('reason') }}</textarea>
            @error('reason')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Maximum Uses (Optional)</label>
            <input type="number" name="max_uses" class="form-control" value="{{ old('max_uses') }}" min="1" placeholder="Leave empty for 1 use">
            @error('max_uses')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
            <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">Maximum number of times this RegID can be bypassed at each selected location. Leave empty for 1 use (default). If you enter a number (e.g., 5), it will allow that many uses. After max uses are reached, it will work normally as per category.</p>
        </div>

        <div class="form-group">
            <label class="form-label">Bypass At Locations *</label>
            <p style="font-size: 14px; color: #6b7280; margin-bottom: 10px;">Select locations where this RegID will be allowed once (then follows category rules):</p>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; max-height: 300px; overflow-y: auto; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px;">
                @foreach($locations as $location)
                    <label style="display: flex; align-items: center; gap: 8px; padding: 8px; cursor: pointer; border-radius: 4px; transition: background-color 0.2s;">
                        <input type="checkbox" name="locations[]" value="{{ $location->id }}" {{ in_array($location->id, old('locations', [])) ? 'checked' : '' }}>
                        <span>{{ $location->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('locations')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
            @error('locations.*')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Create Bypassed RegID</button>
            <a href="{{ route('admin.bypassed-regids.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
