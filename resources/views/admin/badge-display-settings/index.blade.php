@extends('layouts.app')

@section('title', 'Badge Display Settings')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="card-title">Badge Display Settings</h1>
        <a href="{{ route('admin.badge-display-settings.create') }}" class="btn btn-primary">Add New Settings</a>
    </div>

    <p style="margin-bottom: 20px; color: #6b7280;">
        Configure which fields should be displayed on badges for each category.
    </p>

    <table class="table">
        <thead>
            <tr>
                <th>Category</th>
                <th>Settings</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
                @php
                    $setting = $settings->get(trim($category->Category));
                @endphp
                <tr>
                    <td><strong>{{ $category->Category }}</strong></td>
                    <td>
                        @if($setting)
                            @php
                                $enabledFields = [];
                                $fields = ['RegID', 'Name', 'Email', 'Mobile', 'Designation', 'Company', 
                                          'Country', 'State', 'City', 'Additional1', 'Additional2', 
                                          'Additional3', 'Additional4', 'Additional5', 'QRcode'];
                                foreach($fields as $field) {
                                    // Check both boolean cast and raw value
                                    $value = $setting->getAttribute($field);
                                    if($value == 1 || $value === true || $value == '1') {
                                        $enabledFields[] = $field;
                                    }
                                }
                            @endphp
                            <span style="color: #059669;">{{ count($enabledFields) }} fields enabled</span>
                        @else
                            <span style="color: #dc2626;">Not configured</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.badge-display-settings.edit', $category->Category) }}" class="btn btn-secondary" style="padding: 6px 12px; font-size: 12px;">
                            {{ $setting ? 'Edit' : 'Configure' }}
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" style="text-align: center; padding: 40px;">
                        No categories found. <a href="{{ route('admin.categories.create') }}">Create a category first</a>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
