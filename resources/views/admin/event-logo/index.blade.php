@extends('layouts.app')

@section('title', 'Event Logo')

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Event Logo Management</h1>
    </div>

    <div style="padding: 20px;">
        <p style="margin-bottom: 20px; color: #6b7280;">
            Upload an event logo that will be displayed on all pages of the system.
        </p>

        @if($settings->logo_path)
            <div style="margin-bottom: 30px; padding: 20px; background-color: #f9fafb; border-radius: 8px; text-align: center;">
                <h3 style="margin-bottom: 15px; font-size: 16px;">Current Logo</h3>
                <img src="{{ Storage::url($settings->logo_path) }}" 
                     alt="Event Logo" 
                     style="max-width: 300px; max-height: 150px; object-fit: contain; border: 1px solid #e5e7eb; padding: 10px; background: white; border-radius: 8px;">
            </div>
        @endif

        <form action="{{ route('admin.event-logo.upload') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group">
                <label class="form-label">
                    @if($settings->logo_path)
                        Replace Logo
                    @else
                        Upload Logo
                    @endif
                    <span style="color: #ef4444;">*</span>
                </label>
                <input type="file" 
                       name="logo" 
                       class="form-control" 
                       accept="image/*"
                       required>
                <small style="color: #6b7280; display: block; margin-top: 5px;">
                    Supported formats: JPEG, PNG, JPG, GIF, SVG. Max size: 2MB
                </small>
                @error('logo')
                    <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                @enderror
            </div>

            <div style="margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    @if($settings->logo_path)
                        Update Logo
                    @else
                        Upload Logo
                    @endif
                </button>
            </div>
        </form>
        
        @if($settings->logo_path)
            <div style="margin-top: 20px;">
                <form action="{{ route('admin.event-logo.delete') }}" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="btn btn-danger" 
                            onclick="return confirm('Are you sure you want to delete the event logo?');">
                        Delete Logo
                    </button>
                </form>
            </div>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-top: 20px;">
        {{ session('success') }}
    </div>
@endif
@endsection
