@extends('layouts.app')

@section('title', 'Badge Layout Editor')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Badge Layout Editor</h1>
    </div>

    <p style="margin-bottom: 20px; color: #6b7280;">
        Please select a category from the dropdown on the Layout Editor page to configure its badge.
    </p>

    <div style="text-align: center; margin-top: 20px;">
        @php
            $firstCategory = \App\Models\Category::first();
        @endphp
        @if($firstCategory)
            <a href="{{ route('admin.badge-layout.edit', $firstCategory->Category) }}" class="btn btn-primary">
                Go to Layout Editor
            </a>
        @else
            <p style="color: #6b7280;">No categories found. <a href="{{ route('admin.categories.create') }}">Create a category first</a></p>
        @endif
    </div>
</div>
@endsection
