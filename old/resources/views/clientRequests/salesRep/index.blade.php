@extends('layouts.master')
@section('title', 'All Edit Requests')

@section('content')
<div class="container-fluid px-4 py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h3 fw-bold mb-2">Client Edit Requests</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Edit Requests</li>
                </ol>
            </nav>
        </div>
        <div>
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-funnel me-2"></i>Filter
                </button>
                <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => '']) }}">All Requests</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'pending']) }}">Pending</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'approved']) }}">Approved</a></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['status' => 'rejected']) }}">Rejected</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ request()->fullUrlWithQuery(['urgent' => 'true']) }}">Urgent (Older than 1 day)</a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-white border-bottom py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">
                    <i class="bi bi-list-check me-2 text-primary"></i>
                    Request List
                </h5>
                <div class="text-muted small">
                    Showing {{ $requests->firstItem() }} - {{ $requests->lastItem() }} of {{ $requests->total() }} requests
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            @if ($requests->count())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Client</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($requests as $request)
                        <tr class="{{ $request->created_at < now()->subDay() && $request->status === 'pending' ? 'bg-urgent-light' : '' }}">
                            <td class="ps-4 fw-semibold">
                                {{ $loop->iteration + ($requests->currentPage() - 1) * $requests->perPage() }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-3 bg-primary bg-opacity-10 text-primary rounded-circle">
                                        <i class="bi bi-building fs-5"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 fw-semibold">{{ $request->client->company_name }}</p>
                                        <small class="text-muted">ID: {{ $request->client_id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    {{ \App\Models\ClientEditRequest::REQUEST_TYPES[$request->request_type] ?? ucfirst($request->request_type) }}
                                </span>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $request->description }}">
                                    {{ $request->description }}
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill py-1 px-3
                                    @if($request->status === 'approved') bg-success bg-opacity-10 text-success
                                    @elseif($request->status === 'rejected') bg-danger bg-opacity-10 text-danger
                                    @else bg-warning bg-opacity-10 text-warning
                                    @endif">
                                    <i class="bi
                                        @if($request->status === 'approved') bi-check-circle
                                        @elseif($request->status === 'rejected') bi-x-circle
                                        @else bi-hourglass
                                        @endif me-1">
                                    </i>
                                    {{ ucfirst($request->status) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted" title="{{ $request->created_at->format('M d, Y h:i A') }}">
                                    {{ $request->created_at->diffForHumans() }}
                                </small>
                            </td>
                            <td class="text-end pe-4">
                                <a href="{{ route('sales-reps.client-requests.show', ['client' => $request->client_id, 'client_request' => $request->id]) }}"
                                   class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="bi bi-eye me-1"></i> View
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                </div>
                <h5 class="text-muted mb-3">No edit requests found</h5>
                <p class="text-muted">When clients submit edit requests, they'll appear here</p>
            </div>
            @endif
        </div>

        @if ($requests->hasPages())
        <div class="card-footer bg-white border-top py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $requests->firstItem() }} - {{ $requests->lastItem() }} of {{ $requests->total() }}
                </div>
                <div>
                    {{ $requests->onEachSide(1)->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
    }
    .bg-urgent-light {
        background-color: rgba(255, 193, 7, 0.1) !important;
        border-left: 3px solid #ffc107;
    }
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    .badge {
        font-weight: 500;
    }
</style>
@endpush
