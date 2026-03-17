@extends('layouts.app')

@section('title', 'Scanning Type')

@section('content')
<div class="card" style="max-width: 720px; margin: 0 auto;">
    <div class="card-header">
        <h1 class="card-title">Scanning Type</h1>
    </div>

    <form method="POST" action="{{ route('admin.scanning.type.update') }}">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">Select Scanning Method</label>
            <div style="display: grid; gap: 10px; margin-top: 8px;">
                <label class="lead-field-checkbox" style="padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
                    <input type="radio"
                           name="scanning_type"
                           value="camera"
                           {{ ($eventSettings->scanning_type ?? 'camera') === 'camera' ? 'checked' : '' }}>
                    <span><strong>Camera Scanning</strong> - Show camera scanner only</span>
                </label>
                <label class="lead-field-checkbox" style="padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;">
                    <input type="radio"
                           name="scanning_type"
                           value="device"
                           {{ ($eventSettings->scanning_type ?? 'camera') === 'device' ? 'checked' : '' }}>
                    <span><strong>Scanner Device</strong> - Show text input only (no camera permission)</span>
                </label>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Save Scanning Type</button>
    </form>
</div>
@endsection

