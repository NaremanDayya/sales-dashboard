@extends('layouts.master')
@section('title', 'Agreement Edit Request Details')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div>
            <h1 class="h3 fw-bold mb-2">Agreement Edit Request Details</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('agreement-request.index') }}">Agreement Edit Requests</a></li>
                    <li class="breadcrumb-item active" aria-current="page">#{{ $request->id }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <span class="badge fs-6
                @if($request->status === 'approved') bg-success
                @elseif($request->status === 'rejected') bg-danger
                @else bg-warning text-dark
                @endif">
                {{ ucfirst($request->status) }}
            </span>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <div class="card-header bg-primary bg-opacity-10 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-semibold">Edit Request #{{ $request->id }}</h5>
                <div class="text-muted">
                    Created: {{ $request->created_at->format('M d, Y h:i A') }}
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <h6 class="text-muted text-uppercase small">Client</h6>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3 bg-primary bg-opacity-10 text-primary rounded-circle">
                                <i class="bi bi-building fs-5"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-semibold">{{ $request->client->company_name }}</p>
                                <small class="text-muted">ID: {{ $request->client_id }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <h6 class="text-muted text-uppercase small">Sales Representative</h6>
                        <div class="d-flex align-items-center">
                            <div class="avatar avatar-sm me-3 bg-info bg-opacity-10 text-info rounded-circle">
                                <i class="bi bi-person fs-5"></i>
                            </div>
                            <div>
                                <p class="mb-0 fw-semibold">{{ $request->salesRep->name ?? 'Unassigned' }}</p>
                                @if($request->salesRep)
                                <small class="text-muted">ID: {{ $request->sales_rep_id }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Agreement Section --}}
            <div class="mb-4">
                <h6 class="text-muted text-uppercase small">Agreement</h6>
                <div class="card bg-light border-0 p-3">
                    <p class="mb-0 fw-semibold">Agreement ID: {{ $request->agreement->id }}</p>
                    <p class="mb-0">Created: {{ $request->agreement->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-4">
                        <h6 class="text-muted text-uppercase small">Request Type</h6>
                        <p class="fw-semibold">
                            <i class="bi bi-tag-fill me-2 text-primary"></i>
                            {{ ucfirst($request->request_type) }}
                        </p>
                    </div>
                </div>

                <div class="col-md-6">
                    @if($request->response_date)
                    <div class="mb-4">
                        <h6 class="text-muted text-uppercase small">Response Date</h6>
                        <p class="fw-semibold">
                            <i class="bi bi-calendar-check-fill me-2 text-primary"></i>
                            {{ $request->response_date->format('M d, Y h:i A') }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="mb-4">
                <h6 class="text-muted text-uppercase small">Description</h6>
                <div class="card bg-light border-0 p-3">
                    <p class="mb-0">{{ $request->description ?? 'No description provided' }}</p>
                </div>
            </div>

            @if($request->edited_field)
            <div class="mb-4">
                <h6 class="text-muted text-uppercase small">Edited Field</h6>
                <div class="card bg-light border-0 p-3">
                    <p class="mb-0">{{ $request->edited_field }}</p>
                </div>
            </div>
            @endif

            @if($request->notes)
            <div class="mb-4">
                <h6 class="text-muted text-uppercase small">Admin Notes</h6>
                <div class="card bg-light border-0 p-3">
                    <p class="mb-0">{{ $request->notes }}</p>
                </div>
            </div>
            @endif

            <div class="d-flex justify-content-between mt-5">
                <a href="{{ route('agreement-request.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i> Back to Requests
                </a>

                @if($request->status === 'pending')
                <div>
                    <button class="btn btn-success me-2">
                        <i class="bi bi-check-circle me-2"></i> Approve
                    </button>
                    <button class="btn btn-danger">
                        <i class="bi bi-x-circle me-2"></i> Reject
                    </button>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
    }

    .card {
        transition: transform 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
    }
</style>
@endpush
