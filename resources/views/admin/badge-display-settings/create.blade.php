@extends('layouts.app')

@section('title', 'Create Badge Display Settings')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Create Badge Display Settings</h1>
    </div>

    <form action="{{ route('admin.badge-display-settings.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label class="form-label">
                Category
                <span class="tooltip" data-tooltip="Select the category for these display settings">ℹ️</span>
            </label>
            <select name="Category" class="form-control" required>
                <option value="">Select a category</option>
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

        <div style="margin: 30px 0; padding: 20px; background-color: #f9fafb; border-radius: 8px;">
            <h3 style="margin-bottom: 20px; color: #1e40af;">Select Fields to Display on Badge</h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div class="form-check">
                    <input type="checkbox" name="RegID" id="RegID" value="1" class="form-check-input" {{ old('RegID') ? 'checked' : '' }}>
                    <label class="form-check-label" for="RegID">Registration ID</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Name" id="Name" value="1" class="form-check-input" {{ old('Name') ? 'checked' : '' }}>
                    <label class="form-check-label" for="Name">Name</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Email" id="Email" value="1" class="form-check-input" {{ old('Email') ? 'checked' : '' }}>
                    <label class="form-check-label" for="Email">Email</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Mobile" id="Mobile" value="1" class="form-check-input" {{ old('Mobile') ? 'checked' : '' }}>
                    <label class="form-check-label" for="Mobile">Mobile</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Designation" id="Designation" value="1" class="form-check-input" {{ old('Designation') ? 'checked' : '' }}>
                    <label class="form-check-label" for="Designation">Designation</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Company" id="Company" value="1" class="form-check-input" {{ old('Company') ? 'checked' : '' }}>
                    <label class="form-check-label" for="Company">Company</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Country" id="Country" value="1" class="form-check-input" {{ old('Country') ? 'checked' : '' }}>
                    <label class="form-check-label" for="Country">Country</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="State" id="State" value="1" class="form-check-input" {{ old('State') ? 'checked' : '' }}>
                    <label class="form-check-label" for="State">State</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="City" id="City" value="1" class="form-check-input" {{ old('City') ? 'checked' : '' }}>
                    <label class="form-check-label" for="City">City</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Additional1" id="Additional1" value="1" class="form-check-input" {{ old('Additional1') ? 'checked' : '' }}>
                    <label class="form-check-label" for="Additional1">Additional 1</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Additional2" id="Additional2" value="1" class="form-check-input" {{ old('Additional2') ? 'checked' : '' }}>
                    <label class="form-check-label" for="Additional2">Additional 2</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Additional3" id="Additional3" value="1" class="form-check-input" {{ old('Additional3') ? 'checked' : '' }}>
                    <label class="form-check-label" for="Additional3">Additional 3</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Additional4" id="Additional4" value="1" class="form-check-input" {{ old('Additional4') ? 'checked' : '' }}>
                    <label class="form-check-label" for="Additional4">Additional 4</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Additional5" id="Additional5" value="1" class="form-check-input" {{ old('Additional5') ? 'checked' : '' }}>
                    <label class="form-check-label" for="Additional5">Additional 5</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="QRcode" id="QRcode" value="1" class="form-check-input" {{ old('QRcode') ? 'checked' : '' }}>
                    <label class="form-check-label" for="QRcode">QR Code</label>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Save Settings</button>
            <a href="{{ route('admin.badge-display-settings.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
