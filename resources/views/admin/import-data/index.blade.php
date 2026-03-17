@extends('layouts.app')

@section('title', 'Import Data')

@section('content')
    <div class="card">
        <div class="card-header">
            <h1 class="card-title">Import Data (Excel)</h1>
        </div>

        <div style="display: grid; grid-template-columns: 1fr; gap: 20px;">
            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title" style="font-size: 18px;">1) Download Excel Template</h2>
                </div>

                <div style="color: #374151; margin-bottom: 12px; font-size: 14px;">
                    This template is common for all categories and contains all printable columns from <b>user_details</b>.
                </div>

                <a href="{{ route('admin.import-data.template') }}" class="btn btn-primary">Download Template</a>
            </div>

            <div class="card" style="margin-bottom: 0;">
                <div class="card-header">
                    <h2 class="card-title" style="font-size: 18px;">2) Upload Filled Excel</h2>
                </div>

                <form method="POST" action="{{ route('admin.import-data.import') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="form-group">
                        <label class="form-label">Category</label>
                        <select name="category" class="form-control" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->Category }}">{{ $cat->Category }}</option>
                            @endforeach
                        </select>
                        @error('category')
                            <div style="color:#991b1b; margin-top: 8px; font-size: 13px;">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label">Excel File</label>
                        <input type="file" name="file" class="form-control" accept=".xlsx,.xls,.csv" required>
                        @error('file')
                            <div style="color:#991b1b; margin-top: 8px; font-size: 13px;">{{ $message }}</div>
                        @enderror
                        <small style="color: #6b7280; font-size: 12px; margin-top: 8px; display: block;">
                            Leave any field blank to save it blank in database. If <b>RegID</b> is blank, it will be generated automatically.
                        </small>
                    </div>

                    <button type="submit" class="btn btn-primary">Upload & Import</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary" style="margin-left: 10px;">Back</a>
                </form>
            </div>
        </div>
    </div>
@endsection

