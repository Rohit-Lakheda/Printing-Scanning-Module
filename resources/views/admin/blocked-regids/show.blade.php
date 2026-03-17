@extends('layouts.app')

@section('title', 'View Blocked RegID')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Blocked RegID Details</h1>
    </div>

    <div style="padding: 20px;">
        <div style="margin-bottom: 20px;">
            <strong>RegID:</strong> {{ $blockedRegid->regid }}
        </div>

        <div style="margin-bottom: 20px;">
            <strong>Reason:</strong> {{ $blockedRegid->reason ?? 'N/A' }}
        </div>

        <div style="margin-bottom: 20px;">
            <strong>Blocked At Locations:</strong>
            @if($blockedRegid->locations->count() > 0)
                <ul style="margin-top: 10px; padding-left: 20px;">
                    @foreach($blockedRegid->locations as $location)
                        <li>{{ $location->name }}</li>
                    @endforeach
                </ul>
            @else
                <p style="color: #ef4444; margin-top: 10px;">No locations assigned</p>
            @endif
        </div>

        <div style="margin-top: 30px;">
            <a href="{{ route('admin.blocked-regids.edit', $blockedRegid) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('admin.blocked-regids.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection
