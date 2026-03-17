@extends('layouts.app')

@section('title', 'Unique Print Settings')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Unique Print & Receipt Number Settings</h1>
    </div>

    <form action="{{ route('admin.unique-print.update') }}" method="POST">
        @csrf
        @method('PUT')

        <table class="table">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Unique Printing</th>
                    <th>Receipt Number Required</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->Category }}</td>
                        <td>
                            <input type="hidden" name="categories[{{ $category->id }}][id]" value="{{ $category->id }}">
                            <input type="checkbox" 
                                   name="categories[{{ $category->id }}][unique_printing]" 
                                   value="1" 
                                   {{ $category->unique_printing ? 'checked' : '' }}>
                            <label>Enable unique printing for this category</label>
                        </td>
                        <td>
                            <input type="checkbox" 
                                   name="categories[{{ $category->id }}][receipt_number_required]" 
                                   value="1" 
                                   {{ $category->receipt_number_required ? 'checked' : '' }}>
                            <label>Require receipt number for this category</label>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="padding: 20px; border-top: 1px solid #e5e7eb;">
            <button type="submit" class="btn btn-primary">Save Settings</button>
        </div>
    </form>
</div>

@if(session('success'))
    <div class="alert alert-success" style="margin-top: 20px;">
        {{ session('success') }}
    </div>
@endif
@endsection
