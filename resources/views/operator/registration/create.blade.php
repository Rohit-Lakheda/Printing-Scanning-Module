@extends('layouts.app')

@section('title', 'Onsite Registration')

@section('content')
<div class="card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h1 class="card-title">Onsite Registration</h1>
    </div>

    <form action="{{ route('operator.registration.store') }}" method="POST" id="registrationForm">
        @csrf

        <div class="form-group">
            <label class="form-label">
                Category
                <span class="tooltip" data-tooltip="Select the category for this registration">ℹ️</span>
            </label>
            <select name="Category" id="categorySelect" class="form-control" required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->Category }}" {{ old('Category') == $category->Category ? 'selected' : '' }}>
                        {{ $category->Category }}
                    </option>
                @endforeach
            </select>
            @error('Category')
                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
            @enderror
        </div>

        <div id="dynamicFields">
            <!-- Fields will be loaded dynamically based on category -->
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Register & Print Badge</button>
            <a href="{{ route('operator.home') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    const categorySelect = document.getElementById('categorySelect');
    const dynamicFields = document.getElementById('dynamicFields');
    
    // Field definitions
    const allFields = {
        'Name': { type: 'text', required: true, label: 'Name', tooltip: 'Full name of the person' },
        'Designation': { type: 'text', required: false, label: 'Designation', tooltip: 'Job title or designation' },
        'Company': { type: 'text', required: false, label: 'Company', tooltip: 'Company or organization name' },
        'Country': { type: 'text', required: false, label: 'Country', tooltip: 'Country name' },
        'State': { type: 'text', required: false, label: 'State', tooltip: 'State or province' },
        'City': { type: 'text', required: false, label: 'City', tooltip: 'City name' },
        'Email': { type: 'email', required: false, label: 'Email', tooltip: 'Email address' },
        'Mobile': { type: 'text', required: false, label: 'Mobile', tooltip: 'Mobile phone number' },
        'Additional1': { type: 'text', required: false, label: 'Additional Field 1', tooltip: 'Additional information' },
        'Additional2': { type: 'text', required: false, label: 'Additional Field 2', tooltip: 'Additional information' },
        'Additional3': { type: 'text', required: false, label: 'Additional Field 3', tooltip: 'Additional information' },
        'Additional4': { type: 'text', required: false, label: 'Additional Field 4', tooltip: 'Additional information' },
        'Additional5': { type: 'text', required: false, label: 'Additional Field 5', tooltip: 'Additional information' },
    };

    function loadEmailMobileOnly() {
        let html = '';
        const alwaysVisibleFields = ['Email', 'Mobile'];
        
        alwaysVisibleFields.forEach(fieldName => {
            const field = allFields[fieldName];
            if (field) {
                html += `
                    <div class="form-group">
                        <label class="form-label">
                            ${field.label}
                            ${field.tooltip ? `<span class="tooltip" data-tooltip="${field.tooltip}">ℹ️</span>` : ''}
                            <span style="color: #6b7280; font-size: 12px;">(Optional)</span>
                        </label>
                        <input type="${field.type}" 
                               name="${fieldName}" 
                               class="form-control" 
                               value="{{ old('${fieldName}') }}">
                        @error('${fieldName}')
                            <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                        @enderror
                    </div>
                `;
            }
        });
        
        dynamicFields.innerHTML = html;
    }

    function loadFieldsForCategory(visibleFields) {
        let html = '';
        const fieldOrder = ['Name', 'Designation', 'Company', 'Country', 'State', 'City', 'Email', 'Mobile', 
                           'Additional1', 'Additional2', 'Additional3', 'Additional4', 'Additional5'];
        
        // Always show Email and Mobile (not required)
        const alwaysVisibleFields = ['Email', 'Mobile'];
        
        fieldOrder.forEach(fieldName => {
            // Show Name (required), Email/Mobile (always visible but not required), and visible fields for badge
            const shouldShow = fieldName === 'Name' || 
                             alwaysVisibleFields.includes(fieldName) || 
                             visibleFields.includes(fieldName);
            
            if (shouldShow) {
                const field = allFields[fieldName];
                if (field) {
                    // Email and Mobile are never required
                    const isRequired = (fieldName === 'Name') || (field.required && !alwaysVisibleFields.includes(fieldName));
                    
                    html += `
                        <div class="form-group">
                            <label class="form-label">
                                ${field.label}
                                ${field.tooltip ? `<span class="tooltip" data-tooltip="${field.tooltip}">ℹ️</span>` : ''}
                                ${alwaysVisibleFields.includes(fieldName) ? '<span style="color: #6b7280; font-size: 12px;">(Optional)</span>' : ''}
                            </label>
                            <input type="${field.type}" 
                                   name="${fieldName}" 
                                   class="form-control" 
                                   value="{{ old('${fieldName}') }}"
                                   ${isRequired ? 'required' : ''}>
                            @error('${fieldName}')
                                <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                            @enderror
                        </div>
                    `;
                }
            }
        });
        
        dynamicFields.innerHTML = html;
    }

    function loadAllFields() {
        let html = '';
        const fieldOrder = ['Name', 'Designation', 'Company', 'Country', 'State', 'City', 'Email', 'Mobile', 
                           'Additional1', 'Additional2', 'Additional3', 'Additional4', 'Additional5'];
        
        // Always show Email and Mobile (not required)
        const alwaysVisibleFields = ['Email', 'Mobile'];
        
        fieldOrder.forEach(fieldName => {
            const field = allFields[fieldName];
            if (field) {
                // Email and Mobile are never required, Name is always required
                const isRequired = (fieldName === 'Name') || (field.required && !alwaysVisibleFields.includes(fieldName));
                
                html += `
                    <div class="form-group">
                        <label class="form-label">
                            ${field.label}
                            ${field.tooltip ? `<span class="tooltip" data-tooltip="${field.tooltip}">ℹ️</span>` : ''}
                            ${alwaysVisibleFields.includes(fieldName) ? '<span style="color: #6b7280; font-size: 12px;">(Optional)</span>' : ''}
                        </label>
                        <input type="${field.type}" 
                               name="${fieldName}" 
                               class="form-control" 
                               value="{{ old('${fieldName}') }}"
                               ${isRequired ? 'required' : ''}>
                        @error('${fieldName}')
                            <div style="color: #ef4444; margin-top: 5px; font-size: 12px;">{{ $message }}</div>
                        @enderror
                    </div>
                `;
            }
        });
        
        dynamicFields.innerHTML = html;
    }

    // Load fields based on category
    categorySelect.addEventListener('change', function() {
        const category = this.value;
        if (!category) {
            // Show only Email and Mobile when no category is selected
            loadEmailMobileOnly();
            return;
        }

        // Fetch visible fields from API
        fetch(`/api/category/${encodeURIComponent(category)}/visible-fields`)
            .then(response => response.json())
            .then(visibleFields => {
                if (visibleFields && visibleFields.length > 0) {
                    loadFieldsForCategory(visibleFields);
                } else {
                    // If no settings, show all fields
                    loadAllFields();
                }
            })
            .catch(() => {
                // If error, show all fields
                loadAllFields();
            });
    });

    // Load fields on page load if category is selected
    if (categorySelect.value) {
        categorySelect.dispatchEvent(new Event('change'));
    } else {
        // Show only Email and Mobile when no category is selected
        loadEmailMobileOnly();
    }
</script>
@endpush
@endsection
