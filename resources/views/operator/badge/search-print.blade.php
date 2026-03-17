@extends('layouts.app')

@section('title', 'Search & Print Badge')

@section('content')
<div class="card">
    <div class="card-header">
        <h1 class="card-title">Search &amp; Print Badge</h1>
    </div>

    <form method="GET" action="{{ route('operator.badge.search-print') }}" id="searchForm">
        <div style="display: grid; grid-template-columns: auto 1fr; gap: 15px; margin-bottom: 20px; align-items: start;">
            <div class="form-group" style="min-width: 200px;">
                <label class="form-label">Category</label>
                <select name="category" class="form-control">
                    <option value="" {{ !request()->filled('category') ? 'selected' : '' }}>All Categories</option>
                    @foreach(\App\Models\Category::all() as $cat)
                        <option value="{{ $cat->Category }}" {{ request('category') === $cat->Category ? 'selected' : '' }}>
                            {{ $cat->Category }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Search</label>
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       value="{{ request('search') }}" 
                       placeholder="Search by RegID, Name, Email, Mobile, Company, Country, State, City, or any field..."
                       autofocus>
                <small style="color: #6b7280; font-size: 12px; margin-top: 5px; display: block;">
                    Searches across all fields including RegID, Name, Email, Mobile, Company, Country, State, City, Designation, and Additional fields
                </small>
            </div>
        </div>

        <div style="display: flex; gap: 10px; margin-bottom: 20px;">
            <button type="submit" class="btn btn-primary">Search</button>
            <a href="{{ route('operator.badge.search-print') }}" class="btn btn-secondary">Clear</a>
            <a href="{{ route('operator.badge.menu') }}" class="btn btn-secondary">Back to Menu</a>
        </div>
    </form>

    @if(request()->filled('search') || request()->filled('category'))
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>RegID</th>
                        <th>Category</th>
                        <th>Name</th>
                        <th>Designation</th>
                        <th>Company</th>
                        <th>Country</th>
                        <th>State</th>
                        <th>City</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->RegID }}</td>
                            <td>{{ $user->Category }}</td>
                            <td>{{ $user->Name }}</td>
                            <td>{{ $user->Designation ?? '-' }}</td>
                            <td>{{ $user->Company ?? '-' }}</td>
                            <td>{{ $user->Country ?? '-' }}</td>
                            <td>{{ $user->State ?? '-' }}</td>
                            <td>{{ $user->City ?? '-' }}</td>
                            <td>
                                <form action="{{ route('operator.badge.print') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="regid" value="{{ $user->RegID }}">
                                    <button type="submit" class="btn btn-primary" style="padding: 6px 12px; font-size: 12px;">Print</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 40px;">No users found matching your search criteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="table-card">
            @forelse($users as $user)
                <div class="table-card-item">
                    <div class="card-row">
                        <span class="card-label">RegID:</span>
                        <span class="card-value"><strong>{{ $user->RegID }}</strong></span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">Category:</span>
                        <span class="card-value">{{ $user->Category }}</span>
                    </div>
                    <div class="card-row">
                        <span class="card-label">Name:</span>
                        <span class="card-value">{{ $user->Name }}</span>
                    </div>
                    @if($user->Designation)
                    <div class="card-row">
                        <span class="card-label">Designation:</span>
                        <span class="card-value">{{ $user->Designation }}</span>
                    </div>
                    @endif
                    @if($user->Company)
                    <div class="card-row">
                        <span class="card-label">Company:</span>
                        <span class="card-value">{{ $user->Company }}</span>
                    </div>
                    @endif
                    <div class="card-actions">
                        <form action="{{ route('operator.badge.print') }}" method="POST" style="width: 100%;">
                            @csrf
                            <input type="hidden" name="regid" value="{{ $user->RegID }}">
                            <button type="submit" class="btn btn-primary" style="width: 100%;">Print Badge</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="table-card-item" style="text-align: center; padding: 40px;">
                    No users found matching your search criteria.
                </div>
            @endforelse
        </div>

        @if($users->hasPages())
            <div style="margin-top: 20px; display: flex; justify-content: center; gap: 10px;">
                @if($users->onFirstPage())
                    <span class="btn btn-secondary" style="opacity: 0.5; cursor: not-allowed;">Previous</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="btn btn-secondary">Previous</a>
                @endif

                <span style="padding: 8px 16px; display: inline-flex; align-items: center;">
                    Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
                </span>

                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="btn btn-secondary">Next</a>
                @else
                    <span class="btn btn-secondary" style="opacity: 0.5; cursor: not-allowed;">Next</span>
                @endif
            </div>
        @endif
    @endif
</div>
@endsection
