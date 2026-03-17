@extends('layouts.app')

@section('title', 'Post Data API Configurations')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="card-title">Post Data API Configurations</h1>
        <a href="{{ route('admin.api-configurations.create') }}" class="btn btn-primary">Create New Post Data API</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>API Key</th>
                    <th>Status</th>
                    <th>Endpoint</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($configurations as $config)
                    <tr>
                        <td>{{ $config->id }}</td>
                        <td><strong>{{ $config->name }}</strong></td>
                        <td>
                            <code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ $config->api_key }}</code>
                        </td>
                        <td>
                            @if($config->is_active)
                                <span style="color: #10b981; font-weight: bold;">Active</span>
                            @else
                                <span style="color: #6b7280;">Inactive</span>
                            @endif
                        </td>
                        <td>
                            <code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-size: 11px;">/api/user-registration/{{ $config->api_key }}</code>
                        </td>
                        <td>{{ $config->created_at->format('Y-m-d H:i:s') }}</td>
                        <td>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="{{ route('admin.api-configurations.show', $config) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">View & Share</a>
                                <a href="{{ route('admin.api-configurations.edit', $config) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                                <form action="{{ route('admin.api-configurations.destroy', $config) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure? This will disable the API.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">No API configurations found. <a href="{{ route('admin.api-configurations.create') }}">Create one</a></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="table-card">
        @forelse($configurations as $config)
            <div class="table-card-item">
                <div class="card-row">
                    <span class="card-label">Name:</span>
                    <span class="card-value"><strong>{{ $config->name }}</strong></span>
                </div>
                <div class="card-row">
                    <span class="card-label">API Key:</span>
                    <span class="card-value"><code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-size: 11px;">{{ $config->api_key }}</code></span>
                </div>
                <div class="card-row">
                    <span class="card-label">Status:</span>
                    <span class="card-value">
                        @if($config->is_active)
                            <span style="color: #10b981; font-weight: bold;">Active</span>
                        @else
                            <span style="color: #6b7280;">Inactive</span>
                        @endif
                    </span>
                </div>
                <div class="card-row">
                    <span class="card-label">Endpoint:</span>
                    <span class="card-value"><code style="font-size: 11px;">/api/user-registration/{{ $config->api_key }}</code></span>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.api-configurations.show', $config) }}" class="btn btn-secondary" style="flex: 1;">View & Share</a>
                    <a href="{{ route('admin.api-configurations.edit', $config) }}" class="btn btn-secondary" style="flex: 1;">Edit</a>
                    <form action="{{ route('admin.api-configurations.destroy', $config) }}" method="POST" style="flex: 1; display: inline;" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width: 100%;">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="table-card-item" style="text-align: center; padding: 40px;">
                No API configurations found. <a href="{{ route('admin.api-configurations.create') }}">Create one</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
