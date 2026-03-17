@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Edit Category</h1>
    </div>

    <form action="{{ route('admin.categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">
                Prefix
                <span class="tooltip" data-tooltip="Prefix for registration IDs. This will be used when generating RegID for onsite registrations.">ℹ️</span>
            </label>
            <input type="text" name="Prefix" class="form-control" value="{{ old('Prefix', $category->Prefix) }}" maxlength="10" placeholder="e.g., DEL, VIS">
            @error('Prefix')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">
                Category Name
                <span class="tooltip" data-tooltip="Display name for this category">ℹ️</span>
            </label>
            <input type="text" name="Category" class="form-control" value="{{ old('Category', $category->Category) }}" required>
            @error('Category')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="form-group">
                <label class="form-label">
                    Badge Width (mm)
                    <span class="tooltip" data-tooltip="Width of the badge in millimeters">ℹ️</span>
                </label>
                <input type="number" name="badge_width" class="form-control" value="{{ old('badge_width', $category->badge_width) }}" step="0.01" min="10" max="500" required>
                @error('badge_width')
                    <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label">
                    Badge Height (mm)
                    <span class="tooltip" data-tooltip="Height of the badge in millimeters">ℹ️</span>
                </label>
                <input type="number" name="badge_height" class="form-control" value="{{ old('badge_height', $category->badge_height) }}" step="0.01" min="10" max="500" required>
                @error('badge_height')
                    <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Update Category</button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
