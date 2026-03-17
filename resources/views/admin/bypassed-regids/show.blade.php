@extends('layouts.app')

@section('title', 'View Bypassed RegID')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Bypassed RegID Details</h1>
    </div>

    <div style="padding: 20px;">
        <div style="margin-bottom: 20px;">
            <strong>RegID:</strong> {{ $bypassedRegid->regid }}
        </div>

        <div style="margin-bottom: 20px;">
            <strong>Reason:</strong> {{ $bypassedRegid->reason }}
        </div>

        <div style="margin-bottom: 20px;">
            <strong>Maximum Uses:</strong> 
            @if($bypassedRegid->max_uses === null)
                <span>1 time (default)</span>
            @else
                {{ $bypassedRegid->max_uses }} time(s) per location
            @endif
        </div>

        <div style="margin-bottom: 20px;">
            <strong>Bypassed At Locations:</strong>
            @if($bypassedRegid->locations->count() > 0)
                <ul style="margin-top: 10px; padding-left: 20px;">
                    @foreach($bypassedRegid->locations as $location)
                        <li>{{ $location->name }}</li>
                    @endforeach
                </ul>
            @else
                <p style="color: #ef4444; margin-top: 10px;">No locations assigned</p>
            @endif
        </div>

        <div style="margin-bottom: 20px;">
            <strong>Usage History:</strong>
            @if($bypassedRegid->usageLogs->count() > 0)
                @php
                    $usageByLocation = $bypassedRegid->usageLogs->groupBy('location_id');
                @endphp
                <table class="table" style="margin-top: 10px;">
                    <thead>
                        <tr>
                            <th>Location</th>
                            <th>Usage Count</th>
                            <th>Last Used At</th>
                            <th>All Usage Times</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bypassedRegid->locations as $location)
                            @php
                                $locationLogs = $bypassedRegid->usageLogs->where('location_id', $location->id);
                                $usageCount = $locationLogs->count();
                                $lastUsed = $locationLogs->sortByDesc('used_at')->first();
                            @endphp
                            <tr>
                                <td>{{ $location->name }}</td>
                                <td>
                                    <strong>{{ $usageCount }}</strong>
                                    @if($bypassedRegid->max_uses !== null)
                                        / {{ $bypassedRegid->max_uses }}
                                    @endif
                                </td>
                                <td>
                                    @if($lastUsed)
                                        {{ $lastUsed->used_at->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s') }}
                                    @else
                                        <span style="color: #6b7280;">Not used yet</span>
                                    @endif
                                </td>
                                <td>
                                    @if($locationLogs->count() > 0)
                                        <details style="cursor: pointer;">
                                            <summary style="color: #3b82f6;">View all ({{ $locationLogs->count() }})</summary>
                                            <ul style="margin-top: 5px; padding-left: 20px; font-size: 12px;">
                                                @foreach($locationLogs->sortByDesc('used_at') as $log)
                                                    <li>{{ $log->used_at->setTimezone(config('app.timezone'))->format('Y-m-d H:i:s') }}</li>
                                                @endforeach
                                            </ul>
                                        </details>
                                    @else
                                        <span style="color: #6b7280;">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p style="color: #6b7280; margin-top: 10px;">Not used yet at any location</p>
            @endif
        </div>

        <div style="margin-top: 30px;">
            <a href="{{ route('admin.bypassed-regids.edit', $bypassedRegid) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('admin.bypassed-regids.index') }}" class="btn btn-secondary">Back to List</a>
        </div>
    </div>
</div>
@endsection
