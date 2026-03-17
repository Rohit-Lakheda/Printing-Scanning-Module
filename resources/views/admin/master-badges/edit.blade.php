@extends('layouts.app')

@section('title', 'Edit Master RegID')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Edit Master RegID</h1>
    </div>

    <form action="{{ route('admin.master-regids.update', $master_regid) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Registration ID (RegID) *</label>
            <input type="text" name="regid" class="form-control" value="{{ old('regid', $master_regid->regid) }}" required placeholder="Enter RegID for master RegID">
            @error('regid')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Reason/Notes (Optional)</label>
            <textarea name="reason" class="form-control" rows="3" placeholder="Reason or notes for this master RegID">{{ old('reason', $master_regid->reason) }}</textarea>
            @error('reason')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Allowed At Locations *</label>
            <p style="font-size: 14px; color: #6b7280; margin-bottom: 10px;">Select locations where this RegID will be allowed:</p>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; max-height: 300px; overflow-y: auto; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px;">
                @foreach($locations as $location)
                    <label style="display: flex; align-items: center; gap: 8px; padding: 8px; cursor: pointer; border-radius: 4px; transition: background-color 0.2s;">
                        <input type="checkbox" name="locations[]" value="{{ $location->id }}" {{ in_array($location->id, old('locations', $selectedLocations)) ? 'checked' : '' }}>
                        <span>{{ $location->name }}</span>
                    </label>
                @endforeach
            </div>
            @error('locations')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Update Master RegID</button>
            <a href="{{ route('admin.master-regids.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
