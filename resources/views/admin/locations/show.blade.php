@extends('layouts.app')

@section('title', 'View Location')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Location Details</h1>
    </div>

    <div style="padding: 20px;">
        <div style="margin-bottom: 20px;">
            <strong>Name:</strong> {{ $location->name }}
        </div>

        <div style="margin-bottom: 20px;">
            <strong>Description:</strong> {{ $location->description ?? 'N/A' }}
        </div>

        <div style="margin-bottom: 20px;">
            <strong>Status:</strong> 
            @if($location->is_active)
                <span style="color: #10b981; font-weight: bold;">Active</span>
            @else
                <span style="color: #6b7280;">Inactive</span>
            @endif
        </div>

        <div style="margin-bottom: 20px;">
            <strong>Unique Scanning:</strong> 
            @if($location->unique_scanning)
                <span style="color: #6366f1; font-weight: bold;">Enabled</span>
                <p style="font-size: 12px; color: #6b7280; margin-top: 5px;">
                    Users in allowed categories can only be scanned once at this location.
                </p>
            @else
                <span style="color: #6b7280;">Disabled</span>
            @endif
        </div>

        <div style="margin-bottom: 20px;">
            <strong>Allowed Categories:</strong>
            @if($location->allowedCategories->count() > 0)
                <ul style="margin-top: 10px; padding-left: 20px;">
                    @foreach($location->allowedCategories as $locationCategory)
                        <li>{{ $locationCategory->category }}</li>
                    @endforeach
                </ul>
            @else
                <p style="color: #ef4444; margin-top: 10px;">No categories assigned</p>
            @endif
        </div>

        <div style="margin-top: 30px;">
            <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('admin.locations.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection
