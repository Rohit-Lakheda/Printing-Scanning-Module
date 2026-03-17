@extends('layouts.app')

@section('title', 'Bypassed RegIDs')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="card-title">Bypassed RegIDs</h1>
        <a href="{{ route('admin.bypassed-regids.create') }}" class="btn btn-primary">Create Bypassed RegID</a>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>RegID</th>
                    <th>Bypassed At Locations</th>
                    <th>Max Uses</th>
                    <th>Reason</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bypassedRegids as $bypassedRegid)
                    <tr>
                        <td>{{ $bypassedRegid->id }}</td>
                        <td><strong>{{ $bypassedRegid->regid }}</strong></td>
                        <td>
                            @if($bypassedRegid->locations->count() > 0)
                                {{ $bypassedRegid->locations->pluck('name')->join(', ') }}
                            @else
                                <span style="color: #ef4444;">No locations assigned</span>
                            @endif
                        </td>
                        <td>
                            @if($bypassedRegid->max_uses === null)
                                <span>1 time (default)</span>
                            @else
                                {{ $bypassedRegid->max_uses }} time(s)
                            @endif
                        </td>
                        <td>{{ $bypassedRegid->reason }}</td>
                        <td>
                            <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                                <a href="{{ route('admin.bypassed-regids.show', $bypassedRegid) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">View</a>
                                <a href="{{ route('admin.bypassed-regids.edit', $bypassedRegid) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">Edit</a>
                                <form action="{{ route('admin.bypassed-regids.destroy', $bypassedRegid) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" style="padding: 6px 12px; font-size: 12px;">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;">No bypassed RegIDs found. <a href="{{ route('admin.bypassed-regids.create') }}">Create one</a></td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Mobile Card View -->
    <div class="table-card">
        @forelse($bypassedRegids as $bypassedRegid)
            <div class="table-card-item">
                <div class="card-row">
                    <span class="card-label">ID:</span>
                    <span class="card-value">{{ $bypassedRegid->id }}</span>
                </div>
                <div class="card-row">
                    <span class="card-label">RegID:</span>
                    <span class="card-value"><strong>{{ $bypassedRegid->regid }}</strong></span>
                </div>
                <div class="card-row">
                    <span class="card-label">Bypassed At Locations:</span>
                    <span class="card-value">
                        @if($bypassedRegid->locations->count() > 0)
                            {{ $bypassedRegid->locations->pluck('name')->join(', ') }}
                        @else
                            <span style="color: #ef4444;">No locations assigned</span>
                        @endif
                    </span>
                </div>
                <div class="card-row">
                    <span class="card-label">Max Uses:</span>
                    <span class="card-value">
                        @if($bypassedRegid->max_uses === null)
                            1 time (default)
                        @else
                            {{ $bypassedRegid->max_uses }} time(s)
                        @endif
                    </span>
                </div>
                <div class="card-row">
                    <span class="card-label">Reason:</span>
                    <span class="card-value">{{ $bypassedRegid->reason }}</span>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.bypassed-regids.show', $bypassedRegid) }}" class="btn btn-secondary" style="flex: 1;">View</a>
                    <a href="{{ route('admin.bypassed-regids.edit', $bypassedRegid) }}" class="btn btn-secondary" style="flex: 1;">Edit</a>
                    <form action="{{ route('admin.bypassed-regids.destroy', $bypassedRegid) }}" method="POST" style="flex: 1; display: inline;" onsubmit="return confirm('Are you sure?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width: 100%;">Delete</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="table-card-item" style="text-align: center; padding: 40px;">
                No bypassed RegIDs found. <a href="{{ route('admin.bypassed-regids.create') }}">Create one</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
