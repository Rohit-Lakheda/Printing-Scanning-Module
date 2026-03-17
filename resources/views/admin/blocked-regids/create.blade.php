@extends('layouts.app')

@section('title', 'Block RegID')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Block RegID</h1>
    </div>

    <form action="{{ route('admin.blocked-regids.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">Registration ID (RegID) *</label>
            <input type="text" name="regid" class="form-control" value="{{ old('regid') }}" required placeholder="Enter RegID to block">
            @error('regid')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
            <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">The RegID must exist in the user database.</p>
        </div>

        <div class="form-group">
            <label class="form-label">Reason (Optional)</label>
            <textarea name="reason" class="form-control" rows="3" placeholder="Reason for blocking this RegID">{{ old('reason') }}</textarea>
            @error('reason')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Block At Locations *</label>
            <p style="font-size: 14px; color: #6b7280; margin-bottom: 10px;">Select locations where this RegID will be blocked (even if category is allowed):</p>
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
            <button type="submit" class="btn btn-primary">Block RegID</button>
            <a href="{{ route('admin.blocked-regids.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
