@extends('layouts.app')

@section('title', 'Get Data API Configurations')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="card-title">Get Data API Configurations</h1>
        <a href="{{ route('admin.get-data-api-configurations.create') }}" class="btn btn-primary">Create New Get API</a>
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($configurations as $config)
                    <tr>
                        <td>{{ $config->id }}</td>
                        <td><strong>{{ $config->name }}</strong></td>
                        <td><code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-size: 12px;">{{ $config->api_key }}</code></td>
                        <td>{!! $config->is_active ? '<span style="color:#10b981;font-weight:bold;">Active</span>' : '<span style="color:#6b7280;">Inactive</span>' !!}</td>
                        <td><code style="font-size:11px;">/api/user-data/{{ $config->api_key }}</code></td>
                        <td>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="{{ route('admin.get-data-api-configurations.show', $config) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">View</a>
                                <a href="{{ route('admin.get-data-api-configurations.edit', $config) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                                <form action="{{ route('admin.get-data-api-configurations.destroy', $config) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;">No configurations found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

