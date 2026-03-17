@extends('layouts.app')

@section('title', 'Blocked RegIDs')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="card-title">Blocked RegIDs</h1>
        <a href="{{ route('admin.blocked-regids.create') }}" class="btn btn-primary">Block New RegID</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>RegID</th>
                    <th>Blocked At Locations</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($blockedRegids as $blockedRegid)
                    <tr>
                        <td>{{ $blockedRegid->id }}</td>
                        <td><strong>{{ $blockedRegid->regid }}</strong></td>
                        <td>
                            @if($blockedRegid->locations->count() > 0)
                                {{ $blockedRegid->locations->pluck('name')->join(', ') }}
                            @else
                                <span style="color: #ef4444;">No locations assigned</span>
                            @endif
                        </td>
                        <td>{{ $blockedRegid->reason ?? '-' }}</td>
                        <td>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="{{ route('admin.blocked-regids.show', $blockedRegid) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">View</a>
                                <a href="{{ route('admin.blocked-regids.edit', $blockedRegid) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                                <form action="{{ route('admin.blocked-regids.destroy', $blockedRegid) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px;">No blocked RegIDs found. <a href="{{ route('admin.blocked-regids.create') }}">Block one</a></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="table-card">
        @forelse($blockedRegids as $blockedRegid)
            <div class="table-card-item">
                <div class="card-row">
                    <span class="card-label">ID:</span>
                    <span class="card-value">{{ $blockedRegid->id }}</span>
                </div>
                <div class="card-row">
                    <span class="card-label">RegID:</span>
                    <span class="card-value"><strong>{{ $blockedRegid->regid }}</strong></span>
                </div>
                <div class="card-row">
                    <span class="card-label">Blocked At Locations:</span>
                    <span class="card-value">
                        @if($blockedRegid->locations->count() > 0)
                            {{ $blockedRegid->locations->pluck('name')->join(', ') }}
                        @else
                            <span style="color: #ef4444;">No locations assigned</span>
                        @endif
                    </span>
                </div>
                <div class="card-row">
                    <span class="card-label">Reason:</span>
                    <span class="card-value">{{ $blockedRegid->reason ?? '-' }}</span>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.blocked-regids.show', $blockedRegid) }}" class="btn btn-secondary" style="flex: 1;">View</a>
                    <a href="{{ route('admin.blocked-regids.edit', $blockedRegid) }}" class="btn btn-secondary" style="flex: 1;">Edit</a>
                    <form action="{{ route('admin.blocked-regids.destroy', $blockedRegid) }}" method="POST" style="flex: 1; display: inline;" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width: 100%;">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="table-card-item" style="text-align: center; padding: 40px;">
                No blocked RegIDs found. <a href="{{ route('admin.blocked-regids.create') }}">Block one</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
