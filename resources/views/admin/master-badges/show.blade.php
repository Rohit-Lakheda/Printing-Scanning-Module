@extends('layouts.app')

@section('title', 'View Master RegID')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Master RegID Details</h1>
    </div>

    <div style="padding: 20px;">
        <div style="margin-bottom: 20px;">
            <strong>RegID:</strong> {{ $master_regid->regid }}
        </div>

        <div style="margin-bottom: 20px;">
            <strong>Reason/Notes:</strong> {{ $master_regid->reason ?? 'N/A' }}
        </div>

        <div style="margin-bottom: 20px;">
            <strong>Allowed At Locations:</strong>
            @if($master_regid->locations->count() > 0)
                <ul style="margin-top: 10px; padding-left: 20px;">
                    @foreach($master_regid->locations as $location)
                        <li>{{ $location->name }}</li>
                    @endforeach
                </ul>
            @else
                <p style="color: #ef4444; margin-top: 10px;">No locations assigned</p>
            @endif
        </div>

        <div style="margin-top: 30px;">
            <a href="{{ route('admin.master-regids.edit', $master_regid) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('admin.master-regids.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection
