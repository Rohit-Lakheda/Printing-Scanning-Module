@extends('layouts.app')

@section('title', 'Locations')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="card-title">Locations</h1>
        <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">Add New Location</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Allowed Categories</th>
                    <th>Status</th>
                    <th>Unique Scanning</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locations as $location)
                    <tr>
                        <td>{{ $location->id }}</td>
                        <td>{{ $location->name }}</td>
                        <td>{{ $location->description ?? '-' }}</td>
                        <td>
                            @if($location->allowedCategories->count() > 0)
                                {{ $location->allowedCategories->pluck('category')->join(', ') }}
                            @else
                                <span style="color: #ef4444;">No categories assigned</span>
                            @endif
                        </td>
                        <td>
                            @if($location->is_active)
                                <span style="color: #10b981; font-weight: bold;">Active</span>
                            @else
                                <span style="color: #6b7280;">Inactive</span>
                            @endif
                        </td>
                        <td>
                            @if($location->unique_scanning)
                                <span style="color: #6366f1; font-weight: bold;">Enabled</span>
                            @else
                                <span style="color: #6b7280;">Disabled</span>
                            @endif
                        </td>
                        <td>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="{{ route('admin.locations.show', $location) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">View</a>
                                <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                                <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">No locations found. <a href="{{ route('admin.locations.create') }}">Create one</a></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="table-card">
        @forelse($locations as $location)
            <div class="table-card-item">
                <div class="card-row">
                    <span class="card-label">ID:</span>
                    <span class="card-value">{{ $location->id }}</span>
                </div>
                <div class="card-row">
                    <span class="card-label">Name:</span>
                    <span class="card-value">{{ $location->name }}</span>
                </div>
                <div class="card-row">
                    <span class="card-label">Description:</span>
                    <span class="card-value">{{ $location->description ?? '-' }}</span>
                </div>
                <div class="card-row">
                    <span class="card-label">Allowed Categories:</span>
                    <span class="card-value">
                        @if($location->allowedCategories->count() > 0)
                            {{ $location->allowedCategories->pluck('category')->join(', ') }}
                        @else
                            <span style="color: #ef4444;">No categories assigned</span>
                        @endif
                    </span>
                </div>
                <div class="card-row">
                    <span class="card-label">Status:</span>
                    <span class="card-value">
                        @if($location->is_active)
                            <span style="color: #10b981; font-weight: bold;">Active</span>
                        @else
                            <span style="color: #6b7280;">Inactive</span>
                        @endif
                    </span>
                </div>
                <div class="card-row">
                    <span class="card-label">Unique Scanning:</span>
                    <span class="card-value">
                        @if($location->unique_scanning)
                            <span style="color: #6366f1; font-weight: bold;">Enabled</span>
                        @else
                            <span style="color: #6b7280;">Disabled</span>
                        @endif
                    </span>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.locations.show', $location) }}" class="btn btn-secondary" style="flex: 1;">View</a>
                    <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-secondary" style="flex: 1;">Edit</a>
                    <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" style="flex: 1; display: inline;" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width: 100%;">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="table-card-item" style="text-align: center; padding: 40px;">
                No locations found. <a href="{{ route('admin.locations.create') }}">Create one</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
