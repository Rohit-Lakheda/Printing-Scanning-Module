@extends('layouts.app')

@section('title', 'Get Data API Configuration Details')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="card-title">Get Data API Configuration: {{ $getDataApiConfiguration->name }}</h1>
        <div>
            <a href="{{ route('admin.get-data-api-configurations.edit', $getDataApiConfiguration) }}" class="btn btn-secondary">Edit</a>
            <a href="{{ route('admin.get-data-api-configurations.index') }}" class="btn btn-secondary" style="margin-left: 10px;">Back</a>
        </div>
    </div>

    <div style="padding: 20px;">
        <p><strong>Status:</strong> {!! $getDataApiConfiguration->is_active ? '<span style="color:#10b981;font-weight:bold;">Active</span>' : '<span style="color:#6b7280;">Inactive</span>' !!}</p>

        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="font-size: 18px; margin-bottom: 12px;">Endpoint</h3>
            <code style="display:block; background:#fff; padding:12px; border:1px solid #e5e7eb; border-radius:6px;">GET {{ $endpointUrl }}</code>
        </div>

        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="font-size: 18px; margin-bottom: 12px;">API Key</h3>
            <code style="display:block; background:#fff; padding:12px; border:1px solid #e5e7eb; border-radius:6px;">{{ $getDataApiConfiguration->api_key }}</code>
        </div>

        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="font-size: 18px; margin-bottom: 12px;">Input Fields</h3>
            <p style="font-size: 13px; color: #6b7280;">Pass all selected fields as query params.</p>
            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:10px;">
                @foreach(($getDataApiConfiguration->input_fields ?? []) as $field)
                    <span style="background:#e0f2fe; color:#0c4a6e; padding:6px 10px; border-radius:999px; font-size:12px;">{{ $field }}</span>
                @endforeach
            </div>
        </div>

        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin: 20px 0;">
            <h3 style="font-size: 18px; margin-bottom: 12px;">Response Fields</h3>
            <div style="display:flex; flex-wrap:wrap; gap:8px; margin-top:10px;">
                @foreach(($getDataApiConfiguration->response_fields ?? []) as $field)
                    <span style="background:#dcfce7; color:#14532d; padding:6px 10px; border-radius:999px; font-size:12px;">{{ $field }}</span>
                @endforeach
            </div>
        </div>

        <div style="background: #eff6ff; padding: 20px; border-radius: 8px; border-left: 4px solid #3b82f6;">
            <h3 style="font-size: 18px; margin-bottom: 12px;">Usage Example</h3>
            @php
                $sampleQuery = collect($getDataApiConfiguration->input_fields ?? [])
                    ->map(fn($field) => $field . '=sample')
                    ->implode('&');
            @endphp
            <code style="display:block; background:#fff; padding:12px; border:1px solid #e5e7eb; border-radius:6px; margin-bottom: 10px;">
                {{ $endpointUrl }}{{ $sampleQuery ? '?' . $sampleQuery : '' }}
            </code>
            <p style="font-size: 13px; color: #6b7280;">Method: <strong>GET</strong> | Response: JSON</p>
        </div>
    </div>
</div>
@endsection

