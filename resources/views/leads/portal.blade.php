@extends('layouts.app')

@section('title', 'Lead Portal')

@push('styles')
<style>
    #lead-portal-container {
        min-height: 70vh;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }
    #lead-portal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    #lead-portal-header h1 {
        font-size: 22px;
        margin: 0;
    }
    #lead-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    #lead-scanner-section {
        display: grid;
        grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
        gap: 16px;
        align-items: flex-start;
    }
    #qr-reader {
        width: 100%;
        max-width: 520px;
    }
    #recent-scans-card {
        max-height: 340px;
        overflow-y: auto;
    }
    #lead-status-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 12px;
        border-radius: 8px;
        border: 1px solid #dbeafe;
        background: #eff6ff;
        margin-bottom: 8px;
        font-size: 13px;
    }
    #lead-sync-btn {
        background: #1d4ed8;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        padding: 8px 12px;
        cursor: pointer;
        font-size: 12px;
    }
    #lead-sync-btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    #lead-connection-status.online {
        color: #065f46;
        font-weight: 700;
    }
    #lead-connection-status.offline {
        color: #b45309;
        font-weight: 700;
    }
    #lead-regid-input {
        width: 100%;
    }
    #lead-result-card {
        border: 1px solid #dbeafe;
        border-radius: 8px;
        padding: 10px;
        background: #f8fafc;
        min-height: 72px;
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }
    #lead-result-title {
        font-weight: 700;
        font-size: 14px;
        margin-bottom: 4px;
    }
    #lead-result-body {
        font-size: 13px;
        color: #334155;
    }
    #lead-result-body .lead-label {
        color: #0f172a;
        font-weight: 600;
    }
    #lead-result-card.state-success {
        background: #ecfdf5;
        border-color: #34d399;
    }
    #lead-result-card.state-warning {
        background: #fffbeb;
        border-color: #f59e0b;
    }
    #lead-result-card.state-error {
        background: #fef2f2;
        border-color: #f87171;
    }
    #lead-meta-modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(2, 6, 23, 0.55);
        z-index: 99999;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    #lead-meta-card {
        background: #ffffff;
        border-radius: 12px;
        width: 100%;
        max-width: 420px;
        padding: 16px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
    }
    .lead-type-options {
        display: flex;
        gap: 12px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }
    @media screen and (max-width: 900px) {
        #lead-scanner-section {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div id="lead-portal-container">
    <div id="lead-portal-header">
        <div>
            <h1>Lead Scanner</h1>
            <div style="font-size:13px;color:#6b7280;">
                Logged in as: <strong>{{ $credential->username }}</strong> ({{ $credential->userDetail->Name ?? '' }})<br>
                Total Leads Captured: <strong id="total-leads-count">{{ $totalLeads ?? 0 }}</strong>
            </div>
        </div>
        <div id="lead-actions">
            <form method="GET" action="{{ route('lead.download') }}" target="_blank">
                <button type="submit" class="btn btn-secondary">Download All Leads (CSV)</button>
            </form>
            <form method="POST" action="{{ route('lead.logout') }}">
                @csrf
                <button type="submit" class="btn btn-danger">Logout</button>
            </form>
        </div>
    </div>

    <div id="lead-scanner-section">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title" style="font-size:18px;">Scan Badges</h2>
            </div>
            <div class="card-body">
                @include('leads._scanner-fragment')
            </div>
        </div>

        <div class="card" id="recent-scans-card">
            <div class="card-header">
                <h2 class="card-title" style="font-size:18px;">Recent Scans</h2>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-0" id="recent-no-data" {{ $recentScans->isEmpty() ? '' : 'style=display:none;' }}>
                    No scans yet. Start scanning to see recent entries here.
                </p>
                <div class="table-responsive" id="recent-table-wrap" {{ $recentScans->isEmpty() ? 'style=display:none;' : '' }}>
                    <table class="table table-sm" id="recent-scans-table">
                        <thead>
                            <tr>
                                @foreach($sharedFields as $field)
                                    <th>{{ $field }}</th>
                                @endforeach
                                <th>Lead Type</th>
                                <th>Comments</th>
                                <th>Scanned At</th>
                            </tr>
                        </thead>
                        <tbody id="recent-scans-tbody">
                            @foreach($recentScans as $scan)
                                <tr>
                                    @foreach($sharedFields as $field)
                                        <td>
                                            @php
                                                $value = optional($scan->userDetail)->{$field} ?? '-';
                                                if ($field === 'Email' && $value && $value !== '-' && str_contains($value, '@')) {
                                                    [$local, $domain] = explode('@', $value, 2);
                                                    $localMasked = strlen($local) <= 2
                                                        ? str_repeat('*', strlen($local))
                                                        : substr($local, 0, 2) . str_repeat('*', max(strlen($local) - 2, 0));
                                                    $value = $localMasked . '@' . $domain;
                                                }
                                                if ($field === 'Mobile' && $value && $value !== '-') {
                                                    $digits = preg_replace('/\D+/', '', (string) $value);
                                                    if (strlen($digits) > 4) {
                                                        $value = substr($digits, 0, 2) . str_repeat('*', max(strlen($digits) - 4, 0)) . substr($digits, -2);
                                                    } elseif (strlen($digits) > 0) {
                                                        $value = str_repeat('*', strlen($digits));
                                                    }
                                                }
                                            @endphp
                                            {{ $value }}
                                        </td>
                                    @endforeach
                                    <td>{{ $scan->lead_type ?? '-' }}</td>
                                    <td>{{ \Illuminate\Support\Str::limit($scan->lead_comments, 35) }}</td>
                                    <td>{{ optional($scan->scanned_at)->format('Y-m-d H:i:s') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="lead-meta-modal">
        <div id="lead-meta-card">
            <h3 style="margin:0 0 8px 0;">Lead Details (Optional)</h3>
            <p style="font-size:13px;color:#64748b;margin-bottom:10px;">
                Choose lead type and add comments if needed.
            </p>
            <div class="lead-type-options">
                <label><input type="radio" name="lead_type" value="hot"> Hot Lead</label>
                <label><input type="radio" name="lead_type" value="warm"> Warm Lead</label>
                <label><input type="radio" name="lead_type" value="cold"> Cold Lead</label>
            </div>
            <textarea id="lead-meta-comments" class="form-control" rows="3" placeholder="Comments (optional)"></textarea>
            <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:12px;">
                <button type="button" id="lead-meta-skip-btn" class="btn btn-secondary">Skip</button>
                <button type="button" id="lead-meta-save-btn" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
window.leadPortalSharedFields = @json($sharedFields ?? []);
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function () {
        navigator.serviceWorker.register('/lead-sw.js').catch(function () {});
    });
}
</script>
@endpush

