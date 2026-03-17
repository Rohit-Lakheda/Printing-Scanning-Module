@extends('layouts.app')

@section('title', 'Edit Badge Display Settings')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Edit Badge Display Settings - {{ $setting->Category }}</h1>
    </div>

    <form action="{{ route('admin.badge-display-settings.update', $setting->Category) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label class="form-label">
                Category
                <span class="tooltip" data-tooltip="Category for these display settings">ℹ️</span>
            </label>
            <input type="text" class="form-control" value="{{ $setting->Category }}" disabled style="background-color: #f3f4f6;">
            <input type="hidden" name="Category" value="{{ $setting->Category }}">
        </div>

        <div style="margin: 30px 0; padding: 20px; background-color: #f9fafb; border-radius: 8px;">
            <h3 style="margin-bottom: 20px; color: #1e40af;">Select Fields to Display on Badge</h3>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div class="form-check">
                    <input type="checkbox" name="RegID" id="RegID" value="1" class="form-check-input" {{ $setting->RegID ? 'checked' : '' }}>
                    <label class="form-check-label" for="RegID">Registration ID</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Name" id="Name" value="1" class="form-check-input" {{ $setting->Name ? 'checked' : '' }}>
                    <label class="form-check-label" for="Name">Name</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Email" id="Email" value="1" class="form-check-input" {{ $setting->Email ? 'checked' : '' }}>
                    <label class="form-check-label" for="Email">Email</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Mobile" id="Mobile" value="1" class="form-check-input" {{ $setting->Mobile ? 'checked' : '' }}>
                    <label class="form-check-label" for="Mobile">Mobile</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Designation" id="Designation" value="1" class="form-check-input" {{ $setting->Designation ? 'checked' : '' }}>
                    <label class="form-check-label" for="Designation">Designation</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Company" id="Company" value="1" class="form-check-input" {{ $setting->Company ? 'checked' : '' }}>
                    <label class="form-check-label" for="Company">Company</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Country" id="Country" value="1" class="form-check-input" {{ $setting->Country ? 'checked' : '' }}>
                    <label class="form-check-label" for="Country">Country</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="State" id="State" value="1" class="form-check-input" {{ $setting->State ? 'checked' : '' }}>
                    <label class="form-check-label" for="State">State</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="City" id="City" value="1" class="form-check-input" {{ $setting->City ? 'checked' : '' }}>
                    <label class="form-check-label" for="City">City</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Additional1" id="Additional1" value="1" class="form-check-input" {{ $setting->Additional1 ? 'checked' : '' }}>
                    <label class="form-check-label" for="Additional1">Additional 1</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Additional2" id="Additional2" value="1" class="form-check-input" {{ $setting->Additional2 ? 'checked' : '' }}>
                    <label class="form-check-label" for="Additional2">Additional 2</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Additional3" id="Additional3" value="1" class="form-check-input" {{ $setting->Additional3 ? 'checked' : '' }}>
                    <label class="form-check-label" for="Additional3">Additional 3</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Additional4" id="Additional4" value="1" class="form-check-input" {{ $setting->Additional4 ? 'checked' : '' }}>
                    <label class="form-check-label" for="Additional4">Additional 4</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="Additional5" id="Additional5" value="1" class="form-check-input" {{ $setting->Additional5 ? 'checked' : '' }}>
                    <label class="form-check-label" for="Additional5">Additional 5</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="QRcode" id="QRcode" value="1" class="form-check-input" {{ $setting->QRcode ? 'checked' : '' }}>
                    <label class="form-check-label" for="QRcode">QR Code</label>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn btn-primary">Update Settings</button>
            <a href="{{ route('admin.badge-display-settings.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
