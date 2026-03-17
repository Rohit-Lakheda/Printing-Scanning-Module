@extends('layouts.app')

@section('title', 'Scan & Print Badge')

@section('content')
<div class="card" style="max-width: 600px; margin: 50px auto;">
    <div class="card-header">
        <h1 class="card-title" style="text-align: center;">Scan &amp; Print Badge</h1>
    </div>

    <form action="{{ route('operator.badge.print') }}" method="POST" id="searchForm">
        @csrf
        <div class="form-group">
            <label class="form-label" style="text-align: center; display: block; font-size: 14px; margin-bottom: 10px;">
                Enter Registration ID
                <span class="tooltip" data-tooltip="Type the registration ID and press Enter to print the badge">ℹ️</span>
            </label>
            <input type="text" 
                   name="regid" 
                   id="regidInput" 
                   class="form-control" 
                   placeholder="e.g., DEL0001, VIS0001" 
                   autofocus
                   required
                   style="font-size: 16px; text-align: center; padding: 14px;">
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <button type="submit" class="btn btn-primary" style="padding: 12px 30px; font-size: 14px;">Search &amp; Print</button>
        </div>
    </form>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ route('operator.home') }}" class="btn btn-secondary">Go to Home</a>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        const regid = document.getElementById('regidInput').value.trim();
        if (!regid) {
            e.preventDefault();
            alert('Please enter a registration ID');
            return false;
        }
    });

    // Auto-submit on Enter
    document.getElementById('regidInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('searchForm').submit();
        }
    });
</script>
@endpush
@endsection
