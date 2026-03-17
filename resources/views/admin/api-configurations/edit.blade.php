@extends('layouts.app')

@section('title', 'Edit Post Data API Configuration')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Edit Post Data API Configuration: {{ $apiConfiguration->name }}</h1>
    </div>

    <form action="{{ route('admin.api-configurations.update', $apiConfiguration) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">API Name <span style="color: #ef4444;">*</span></label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $apiConfiguration->name) }}" required placeholder="e.g., External Registration API">
            @error('name')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" rows="3" placeholder="Optional description of this API">{{ old('description', $apiConfiguration->description) }}</textarea>
            @error('description')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <div class="form-check">
                <input type="checkbox" name="is_active" id="is_active" value="1" class="form-check-input" {{ old('is_active', $apiConfiguration->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Active (API will be enabled)</label>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Field Mappings (Optional)</label>
            <p style="font-size: 13px; color: #6b7280; margin-bottom: 15px;">
                Map API field names to database columns. If left empty, default mappings will be used.
            </p>
            
            <div id="field-mappings-container">
                @php
                    $existingMappings = $apiConfiguration->field_mappings ?? [];
                @endphp
                @if(!empty($existingMappings))
                    @foreach($existingMappings as $apiField => $dbColumn)
                        <div class="field-mapping-row" style="display: grid; grid-template-columns: 1fr 1fr 50px; gap: 10px; margin-bottom: 10px; align-items: center;">
                            <div>
                                <input type="text" name="field_mappings_temp[{{ $apiField }}]" class="form-control" value="{{ $apiField }}" required>
                                <small style="color: #6b7280;">API sends this field</small>
                            </div>
                            <div>
                                <select name="field_mappings_db_temp[{{ $apiField }}]" class="form-control" required>
                                    @foreach($dbColumns as $col => $desc)
                                        <option value="{{ $col }}" {{ $col === $dbColumn ? 'selected' : '' }}>{{ $col }}</option>
                                    @endforeach
                                </select>
                                <small style="color: #6b7280;">Maps to DB column</small>
                            </div>
                            <div>
                                <button type="button" class="btn btn-danger" onclick="this.closest('.field-mapping-row').remove()" style="padding: 6px 12px;">×</button>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
            
            <button type="button" id="add-mapping" class="btn btn-secondary" style="margin-top: 10px;">Add Field Mapping</button>
            <p style="font-size: 12px; color: #6b7280; margin-top: 10px;">
                <strong>Note:</strong> If no custom mappings are added, default field names will be used.
            </p>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Update Post Data API Configuration</button>
            <a href="{{ route('admin.api-configurations.show', $apiConfiguration) }}" class="btn btn-secondary" style="margin-left: 10px;">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('field-mappings-container');
    const addBtn = document.getElementById('add-mapping');
    let rowCount = {{ count($apiConfiguration->field_mappings ?? []) }};

    addBtn.addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'field-mapping-row';
        row.style.cssText = 'display: grid; grid-template-columns: 1fr 1fr 50px; gap: 10px; margin-bottom: 10px; align-items: center;';
        
        const dbColumns = @json($dbColumns);
        const dbOptions = Object.keys(dbColumns).map(col => `<option value="${col}">${col}</option>`).join('');
        
        row.innerHTML = `
            <div>
                <input type="text" name="field_mappings_temp[field${rowCount}]" class="form-control" placeholder="API Field Name" required>
                <small style="color: #6b7280;">API sends this field</small>
            </div>
            <div>
                <select name="field_mappings_db_temp[field${rowCount}]" class="form-control" required>
                    ${dbOptions}
                </select>
                <small style="color: #6b7280;">Maps to DB column</small>
            </div>
            <div>
                <button type="button" class="btn btn-danger" onclick="this.closest('.field-mapping-row').remove()" style="padding: 6px 12px;">×</button>
            </div>
        `;
        
        container.appendChild(row);
        rowCount++;
    });

    // Process form submission to combine field mappings
    document.querySelector('form').addEventListener('submit', function(e) {
        const rows = container.querySelectorAll('.field-mapping-row');
        const mappings = {};
        
        rows.forEach((row) => {
            const apiFieldInput = row.querySelector('input[type="text"]');
            const dbColumnSelect = row.querySelector('select');
            
            if (apiFieldInput && dbColumnSelect) {
                const apiField = apiFieldInput.value.trim();
                const dbColumn = dbColumnSelect.value;
                
                if (apiField && dbColumn) {
                    mappings[apiField] = dbColumn;
                }
            }
        });
        
        // Remove any existing hidden input
        const existingHidden = this.querySelector('input[name="field_mappings"]');
        if (existingHidden) {
            existingHidden.remove();
        }
        
        // Add hidden input with final mappings (only if mappings exist)
        if (Object.keys(mappings).length > 0) {
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'field_mappings';
            hiddenInput.value = JSON.stringify(mappings);
            this.appendChild(hiddenInput);
        }
    });
});
</script>
@endpush
@endsection
