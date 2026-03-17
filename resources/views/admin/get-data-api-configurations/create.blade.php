@extends('layouts.app')

@section('title', 'Create Get Data API Configuration')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Create Get Data API Configuration</h1>
    </div>

    <form action="{{ route('admin.get-data-api-configurations.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">Configuration Name <span style="color: #ef4444;">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
        </div>

        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input" {{ old('is_active', true) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active (API enabled)</label>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Input Fields (what client sends) <span style="color: #ef4444;">*</span></label>
            <div class="lead-fields-grid">
                @foreach($availableFields as $field)
                    <label class="lead-field-checkbox">
                        <input type="checkbox" name="input_fields[]" value="{{ $field }}" {{ in_array($field, old('input_fields', []), true) ? 'checked' : '' }}>
                        <span>{{ $field }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Response Fields (what API returns) <span style="color: #ef4444;">*</span></label>
            <div class="lead-fields-grid">
                @foreach($availableFields as $field)
                    <label class="lead-field-checkbox">
                        <input type="checkbox" name="response_fields[]" value="{{ $field }}" {{ in_array($field, old('response_fields', []), true) ? 'checked' : '' }}>
                        <span>{{ $field }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Create</button>
            <a href="{{ route('admin.get-data-api-configurations.index') }}" class="btn btn-secondary" style="margin-left: 10px;">Cancel</a>
        </div>
    </form>
</div>
@endsection

