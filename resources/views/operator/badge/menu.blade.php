@extends('layouts.app')

@section('title', 'Print Badges')

@section('content')
<div class="card" style="max-width: 700px; margin: 50px auto; text-align: center;">
    <div class="card-header">
        <h1 class="card-title">Print Badges</h1>
    </div>

    <p style="margin-top: 10px; color: #6b7280;">
        Choose how you want to print badges.
    </p>

    <div style="margin-top: 30px; display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px;">
        <a href="{{ route('operator.badge.scan-print') }}" class="btn btn-primary" style="padding: 30px 20px; font-size: 16px;">
            Scan &amp; Print Badge
        </a>
        <a href="{{ route('operator.badge.search-print') }}" class="btn btn-primary" style="padding: 30px 20px; font-size: 16px;">
            Search &amp; Print Badge
        </a>
        <a href="{{ route('operator.badge.bulk.form') }}" class="btn btn-secondary" style="padding: 30px 20px; font-size: 16px;">
            Bulk Print (Grid &amp; PDF)
        </a>
    </div>
</div>
@endsection

