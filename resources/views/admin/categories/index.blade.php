@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="card-title">Categories</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">Add New Category</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Prefix</th>
                    <th>Category Name</th>
                    <th>Badge Size</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->Prefix ?? '-' }}</td>
                        <td>{{ $category->Category }}</td>
                        <td>{{ number_format($category->badge_width, 2) }} x {{ number_format($category->badge_height, 2) }} mm</td>
                        <td>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px;">No categories found. <a href="{{ route('admin.categories.create') }}">Create one</a></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="table-card">
        @forelse($categories as $category)
            <div class="table-card-item">
                <div class="card-row">
                    <span class="card-label">ID:</span>
                    <span class="card-value">{{ $category->id }}</span>
                </div>
                <div class="card-row">
                    <span class="card-label">Prefix:</span>
                    <span class="card-value">{{ $category->Prefix ?? '-' }}</span>
                </div>
                <div class="card-row">
                    <span class="card-label">Category Name:</span>
                    <span class="card-value">{{ $category->Category }}</span>
                </div>
                <div class="card-row">
                    <span class="card-label">Badge Size:</span>
                    <span class="card-value">{{ number_format($category->badge_width, 2) }} x {{ number_format($category->badge_height, 2) }} mm</span>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-secondary" style="flex: 1;">Edit</a>
                    <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="flex: 1; display: inline;" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width: 100%;">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="table-card-item" style="text-align: center; padding: 40px;">
                No categories found. <a href="{{ route('admin.categories.create') }}">Create one</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
