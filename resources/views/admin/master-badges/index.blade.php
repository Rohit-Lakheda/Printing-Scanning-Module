@extends('layouts.app')

@section('title', 'Master RegIDs')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="card-title">Master RegIDs</h1>
        <a href="{{ route('admin.master-regids.create') }}" class="btn btn-primary">Create Master RegID</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>RegID</th>
                    <th>Allowed At Locations</th>
                    <th>Reason/Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($masterBadges as $masterBadge)
                    <tr>
                        <td>{{ $masterBadge->id }}</td>
                        <td><strong>{{ $masterBadge->regid }}</strong></td>
                        <td>
                            @if($masterBadge->locations->count() > 0)
                                {{ $masterBadge->locations->pluck('name')->join(', ') }}
                            @else
                                <span style="color: #ef4444;">No locations assigned</span>
                            @endif
                        </td>
                        <td>{{ $masterBadge->reason ?? '-' }}</td>
                        <td>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="{{ route('admin.master-regids.show', $masterBadge) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">View</a>
                                <a href="{{ route('admin.master-regids.edit', $masterBadge) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                                <form action="{{ route('admin.master-regids.destroy', $masterBadge) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; padding: 40px;">No master RegIDs found. <a href="{{ route('admin.master-regids.create') }}">Create one</a></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="table-card">
        @forelse($masterBadges as $masterBadge)
            <div class="table-card-item">
                <div class="card-row">
                    <span class="card-label">ID:</span>
                    <span class="card-value">{{ $masterBadge->id }}</span>
                </div>
                <div class="card-row">
                    <span class="card-label">RegID:</span>
                    <span class="card-value"><strong>{{ $masterBadge->regid }}</strong></span>
                </div>
                <div class="card-row">
                    <span class="card-label">Allowed At Locations:</span>
                    <span class="card-value">
                        @if($masterBadge->locations->count() > 0)
                            {{ $masterBadge->locations->pluck('name')->join(', ') }}
                        @else
                            <span style="color: #ef4444;">No locations assigned</span>
                        @endif
                    </span>
                </div>
                <div class="card-row">
                    <span class="card-label">Reason/Notes:</span>
                    <span class="card-value">{{ $masterBadge->reason ?? '-' }}</span>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.master-regids.show', $masterBadge) }}" class="btn btn-secondary" style="flex: 1;">View</a>
                    <a href="{{ route('admin.master-regids.edit', $masterBadge) }}" class="btn btn-secondary" style="flex: 1;">Edit</a>
                    <form action="{{ route('admin.master-regids.destroy', $masterBadge) }}" method="POST" style="flex: 1; display: inline;" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width: 100%;">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="table-card-item" style="text-align: center; padding: 40px;">
                No master RegIDs found. <a href="{{ route('admin.master-regids.create') }}">Create one</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
