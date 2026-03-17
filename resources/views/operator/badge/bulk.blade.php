@extends('layouts.app')

@section('title', 'Bulk Print Badges')

@section('content')
<div class="card" style="margin: 30px auto;">
    <div class="card-header">
        <h1 class="card-title">Bulk Print Badges</h1>
    </div>

    <form method="GET" action="{{ route('operator.badge.bulk.form') }}" style="margin-bottom: 20px;">
        <div class="form-group">
            <label class="form-label">Select Category</label>
            <select name="Category" class="form-control" onchange="this.form.submit()">
                <option value="">-- Select Category --</option>
                @foreach(\App\Models\Category::orderBy('Category')->get() as $cat)
                    <option value="{{ $cat->Category }}" {{ $selectedCategory === $cat->Category ? 'selected' : '' }}>
                        {{ $cat->Category }} ({{ number_format($cat->badge_width, 2) }} x {{ number_format($cat->badge_height, 2) }} mm)
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    @if(!$selectedCategory)
        <p style="color: #6b7280; text-align: center; margin-top: 20px;">
            Please select a category to view badge data for bulk printing.
        </p>
    @else
        <form method="POST" action="{{ route('operator.badge.bulk.print') }}" target="_blank" id="bulkGridForm">
            @csrf
            <input type="hidden" name="Category" value="{{ $selectedCategory }}">

            <div style="margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                <div>
                    <label style="font-size: 14px; font-weight: 500;">
                        <input type="checkbox" id="selectAll" style="margin-right: 6px;">
                        Select All
                    </label>
                </div>
                <div>
                    <button type="submit" id="bulkPrintBtn" class="btn btn-secondary" style="padding: 10px 24px; font-size: 14px;" disabled>
                        Bulk Print Selected (PDF)
                    </button>
                </div>
            </div>

            <div style="overflow-x: auto; max-height: 500px; border: 1px solid #e5e7eb; border-radius: 8px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                    <thead>
                        <tr style="background: #eff6ff;">
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: center; width: 40px;">
                                Select
                            </th>
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: left;">RegID</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: left;">Name</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: left;">Designation</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: left;">Company</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: left;">Country</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: left;">State</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: left;">City</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: center;">Print Count</th>
                            <th style="padding: 8px; border-bottom: 1px solid #e5e7eb; text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td style="padding: 6px 8px; border-bottom: 1px solid #f3f4f6; text-align: center;">
                                    <input type="checkbox" name="selected_regids[]" value="{{ $user->RegID }}" class="row-checkbox">
                                </td>
                                <td style="padding: 6px 8px; border-bottom: 1px solid #f3f4f6;">{{ $user->RegID }}</td>
                                <td style="padding: 6px 8px; border-bottom: 1px solid #f3f4f6;">{{ $user->Name }}</td>
                                <td style="padding: 6px 8px; border-bottom: 1px solid #f3f4f6;">{{ $user->Designation }}</td>
                                <td style="padding: 6px 8px; border-bottom: 1px solid #f3f4f6;">{{ $user->Company }}</td>
                                <td style="padding: 6px 8px; border-bottom: 1px solid #f3f4f6;">{{ $user->Country }}</td>
                                <td style="padding: 6px 8px; border-bottom: 1px solid #f3f4f6;">{{ $user->State }}</td>
                                <td style="padding: 6px 8px; border-bottom: 1px solid #f3f4f6;">{{ $user->City }}</td>
                                <td style="padding: 6px 8px; border-bottom: 1px solid #f3f4f6; text-align: center;">
                                    {{ $user->print_count ?? 0 }}
                                </td>
                                <td style="padding: 6px 8px; border-bottom: 1px solid #f3f4f6; text-align: center;">
                                    <a href="{{ route('operator.badge.print', ['regid' => $user->RegID]) }}" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">
                                        Print
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" style="padding: 12px; text-align: center; color: #6b7280;">
                                    No registrations found for this category.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    @endif
</div>

@push('scripts')
<script>
    const selectAll = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    const bulkPrintBtn = document.getElementById('bulkPrintBtn');

    // Function to update button state
    function updateBulkPrintButton() {
        const hasSelection = Array.from(rowCheckboxes).some(cb => cb.checked);
        if (bulkPrintBtn) {
            bulkPrintBtn.disabled = !hasSelection;
            if (hasSelection) {
                bulkPrintBtn.style.opacity = '1';
                bulkPrintBtn.style.cursor = 'pointer';
            } else {
                bulkPrintBtn.style.opacity = '0.5';
                bulkPrintBtn.style.cursor = 'not-allowed';
            }
        }
    }

    // Handle select all checkbox
    if (selectAll) {
        selectAll.addEventListener('change', function () {
            rowCheckboxes.forEach(cb => { cb.checked = selectAll.checked; });
            updateBulkPrintButton();
        });
    }

    // Handle individual checkbox changes
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            // Update select all checkbox state
            if (selectAll) {
                const allChecked = Array.from(rowCheckboxes).every(cb => cb.checked);
                const someChecked = Array.from(rowCheckboxes).some(cb => cb.checked);
                selectAll.checked = allChecked;
                selectAll.indeterminate = someChecked && !allChecked;
            }
            updateBulkPrintButton();
        });
    });

    // Initial button state
    updateBulkPrintButton();
</script>
@endpush
@endsection

