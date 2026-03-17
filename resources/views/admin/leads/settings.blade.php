@extends('layouts.app')

@section('title', 'Lead / QR Settings')

@section('content')
<div class="container">
    <h1 class="mb-4">Lead &amp; Mail Settings</h1>

    <div class="lead-settings-grid">
        <div>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Mail Configuration</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.leads.mail-config.save') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control"
                                   value="{{ old('name', optional($activeMailConfig)->name ?? 'Default') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Host</label>
                            <input type="text" name="host" class="form-control"
                                   value="{{ old('host', optional($activeMailConfig)->host) }}"
                                   required placeholder="smtp.example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Port</label>
                            <input type="number" name="port" class="form-control"
                                   value="{{ old('port', optional($activeMailConfig)->port ?? 587) }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control"
                                   value="{{ old('username', optional($activeMailConfig)->username) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control"
                                   placeholder="{{ $activeMailConfig ? 'Leave blank to keep existing password' : '' }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Encryption</label>
                            <select name="encryption" class="form-select">
                                @php $enc = old('encryption', optional($activeMailConfig)->encryption); @endphp
                                <option value="" {{ $enc === null || $enc === '' ? 'selected' : '' }}>None</option>
                                <option value="ssl" {{ $enc === 'ssl' ? 'selected' : '' }}>SSL</option>
                                <option value="tls" {{ $enc === 'tls' ? 'selected' : '' }}>TLS</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">From Address</label>
                            <input type="email" name="from_address" class="form-control"
                                   value="{{ old('from_address', optional($activeMailConfig)->from_address) }}"
                                   placeholder="no-reply@example.com">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">From Name</label>
                            <input type="text" name="from_name" class="form-control"
                                   value="{{ old('from_name', optional($activeMailConfig)->from_name) }}"
                                   placeholder="Event Team">
                        </div>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="checkbox" name="use_auth" id="use_auth"
                                   {{ old('use_auth', optional($activeMailConfig)->use_auth ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="use_auth">Use Authentication</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                   {{ old('is_active', optional($activeMailConfig)->is_active ?? true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Mail Configuration</button>
                    </form>
                </div>
            </div>
        </div>

        <div>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Send Credentials By Category</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.leads.send-credentials') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Category</label>
                            <select name="category" class="form-select" id="credential-category-select" required>
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->Category }}"
                                        {{ (isset($selectedCategory) && $selectedCategory === $category->Category) ? 'selected' : '' }}>
                                        {{ $category->Category }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Maximum Devices Per User (optional)</label>
                            <input type="number" name="max_devices" class="form-control" min="1" placeholder="Leave blank for unlimited">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Mail Configuration</label>
                            <select name="mail_configuration_id" class="form-select" required>
                                <option value="">Select Configuration</option>
                                @foreach($mailConfigs as $config)
                                    <option value="{{ $config->id }}">{{ $config->name }} ({{ $config->host }}:{{ $config->port }})</option>
                                @endforeach
                            </select>
                        </div>
                        <p class="small text-muted mb-2">
                            This will generate credentials (if missing) and email username/password
                            to all users in the selected category who have an email address, or only selected users below.
                        </p>

                        @if(!empty($selectedCategory))
                            <div class="mb-2 d-flex justify-content-between align-items-center">
                                <label class="form-label mb-0" style="font-size: 13px;">Users in: <strong>{{ $selectedCategory }}</strong></label>
                                <div style="display:flex;gap:8px;align-items:center;">
                                    <input type="text"
                                           id="user-search-input"
                                           class="form-control"
                                           style="max-width:200px;padding:6px 10px;font-size:13px;"
                                           placeholder="Search users"
                                           value="{{ $search ?? '' }}">
                                    <button type="button"
                                            class="btn btn-secondary"
                                            style="padding:6px 12px;font-size:13px;"
                                            id="user-search-button">
                                        Search
                                    </button>
                                </div>
                            </div>

                            @if($users->count() > 0)
                                <div class="table-responsive" style="max-height:320px;overflow-y:auto;margin-bottom:10px;">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th style="width:40px;">
                                                    <input type="checkbox" id="select-all-users">
                                                </th>
                                                <th>RegID</th>
                                                <th>Name</th>
                                                <th>Company</th>
                                                <th>Email</th>
                                                <th>Mobile</th>
                                                <th>Designation</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($users as $user)
                                                <tr>
                                                    <td>
                                                        <input type="checkbox"
                                                               name="user_ids[]"
                                                               value="{{ $user->id }}"
                                                               class="user-select-checkbox">
                                                    </td>
                                                    <td>{{ $user->RegID }}</td>
                                                    <td>{{ $user->Name }}</td>
                                                    <td>{{ $user->Company }}</td>
                                                    <td>
                                                        @php
                                                            $email = $user->Email;
                                                            if ($email && str_contains($email, '@')) {
                                                                [$local, $domain] = explode('@', $email, 2);
                                                                $localMasked = strlen($local) <= 2 ? str_repeat('*', strlen($local)) : substr($local, 0, 2) . str_repeat('*', max(strlen($local) - 2, 0));
                                                                $email = $localMasked . '@' . $domain;
                                                            }
                                                        @endphp
                                                        {{ $email }}
                                                    </td>
                                                    <td>
                                                        @php
                                                            $mobile = $user->Mobile;
                                                            if ($mobile) {
                                                                $digits = preg_replace('/\D+/', '', $mobile);
                                                                if (strlen($digits) > 4) {
                                                                    $mobile = substr($digits, 0, 2) . str_repeat('*', max(strlen($digits) - 4, 0)) . substr($digits, -2);
                                                                } else {
                                                                    $mobile = str_repeat('*', strlen($digits));
                                                                }
                                                            }
                                                        @endphp
                                                        {{ $mobile }}
                                                    </td>
                                                    <td>{{ $user->Designation }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <p class="small text-muted mb-2" id="users-count-text">
                                    Showing {{ $users->count() }} user(s). Select specific users or leave all unchecked to send to the entire category.
                                </p>
                                <p class="small text-muted mb-2" id="users-no-match-text" style="display:none;">
                                    No users match this search.
                                </p>
                            @else
                                <p class="small text-muted mb-2">
                                    No users found in this category for the current search.
                                </p>
                            @endif
                        @endif

                        <button type="submit" class="btn btn-success mt-1">Generate &amp; Send Credentials</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const categorySelect = document.getElementById('credential-category-select');
    const searchInput = document.getElementById('user-search-input');
    const searchButton = document.getElementById('user-search-button');
    const selectAllCheckbox = document.getElementById('select-all-users');
    const userRows = Array.from(document.querySelectorAll('.user-select-checkbox'))
        .map((cb) => cb.closest('tr'))
        .filter(Boolean);
    const usersCountText = document.getElementById('users-count-text');
    const usersNoMatchText = document.getElementById('users-no-match-text');

    function reloadUserList() {
        const baseUrl = "{{ route('admin.leads.settings') }}";
        const params = new URLSearchParams();
        if (categorySelect && categorySelect.value) {
            params.set('category', categorySelect.value);
        }
        const url = params.toString() ? baseUrl + '?' + params.toString() : baseUrl;
        window.location.href = url;
    }

    function applyClientSearchFilter() {
        if (!searchInput || userRows.length === 0) return;
        const term = searchInput.value.trim().toLowerCase();
        let visibleCount = 0;

        userRows.forEach((row) => {
            const haystack = row.textContent.toLowerCase();
            const visible = term === '' || haystack.includes(term);
            row.style.display = visible ? '' : 'none';
            if (visible) visibleCount++;
        });

        if (usersCountText) {
            usersCountText.textContent = 'Showing ' + visibleCount + ' user(s). Select specific users or leave all unchecked to send to the entire category.';
        }
        if (usersNoMatchText) {
            usersNoMatchText.style.display = visibleCount === 0 ? '' : 'none';
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
        }
    }

    if (categorySelect) {
        categorySelect.addEventListener('change', function () {
            reloadUserList();
        });
    }

    if (searchButton) {
        searchButton.addEventListener('click', function () {
            applyClientSearchFilter();
        });
    }

    if (searchInput) {
        searchInput.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                applyClientSearchFilter();
            }
        });
        searchInput.addEventListener('input', function () {
            applyClientSearchFilter();
        });
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function () {
            const checked = selectAllCheckbox.checked;
            document.querySelectorAll('.user-select-checkbox').forEach(function (cb) {
                const row = cb.closest('tr');
                if (!row || row.style.display === 'none') return;
                cb.checked = checked;
            });
        });
    }

    applyClientSearchFilter();
});
</script>
@endpush

