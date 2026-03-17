@extends('layouts.app')

@section('title', 'Create Location')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Create New Location</h1>
    </div>

    <form action="{{ route('admin.locations.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">Location Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
            @error('description')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                <span>Active</span>
            </label>
        </div>

        <div class="form-group">
            <label style="display: flex; align-items: center; gap: 10px;">
                <input type="checkbox" name="unique_scanning" value="1" {{ old('unique_scanning', false) ? 'checked' : '' }}>
                <span>Unique Scanning</span>
            </label>
            <p style="font-size: 12px; color: #6b7280; margin-top: 5px; margin-left: 28px;">
                If enabled, users in allowed categories can only be scanned once at this location. Subsequent scans will show "Already Scanned" message.
            </p>
        </div>

        <div class="form-group">
            <label class="form-label">Allowed Categories</label>
            <p style="font-size: 14px; color: #6b7280; margin-bottom: 10px;">Select which categories are allowed to access this location:</p>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; max-height: 300px; overflow-y: auto; padding: 10px; border: 1px solid #e5e7eb; border-radius: 6px;">
                @foreach($categories as $category)
                    <label style="display: flex; align-items: center; gap: 8px; padding: 8px; cursor: pointer; border-radius: 4px; transition: background-color 0.2s;">
                        <input type="checkbox" name="categories[]" value="{{ $category->Category }}" {{ in_array($category->Category, old('categories', [])) ? 'checked' : '' }}>
                        <span>{{ $category->Category }}</span>
                    </label>
                @endforeach
            </div>
            @error('categories.*')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Create Location</button>
            <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
