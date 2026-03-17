@extends('layouts.app')

@section('title', 'Create Category')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Create New Category</h1>
    </div>

    <form action="{{ route('admin.categories.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">
                Prefix
                <span class="tooltip" data-tooltip="Prefix for registration IDs (e.g., DEL for Delegate). This will be used when generating RegID for onsite registrations.">ℹ️</span>
            </label>
            <input type="text" name="Prefix" class="form-control" value="{{ old('Prefix') }}" maxlength="10" placeholder="e.g., DEL, VIS">
            @error('Prefix')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">
                Category Name
                <span class="tooltip" data-tooltip="Display name for this category (e.g., Delegate, Visitor)">ℹ️</span>
            </label>
            <input type="text" name="Category" class="form-control" value="{{ old('Category') }}" required>
            @error('Category')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">
                    Badge Width (mm)
                    <span class="tooltip" data-tooltip="Width of the badge in millimeters (e.g., 85mm for standard badge)">ℹ️</span>
                </label>
                <input type="number" name="badge_width" class="form-control" value="{{ old('badge_width', 85.00) }}" step="0.01" min="10" max="500" required>
                @error('badge_width')
                    <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    Badge Height (mm)
                    <span class="tooltip" data-tooltip="Height of the badge in millimeters (e.g., 54mm for standard badge)">ℹ️</span>
                </label>
                <input type="number" name="badge_height" class="form-control" value="{{ old('badge_height', 54.00) }}" step="0.01" min="10" max="500" required>
                @error('badge_height')
                    <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Create Category</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
