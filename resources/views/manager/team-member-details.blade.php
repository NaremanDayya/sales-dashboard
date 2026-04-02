@extends('layouts.master')

@section('content')
<div class="app-content">
    <div class="section-header">
        <div>
            <a href="{{ route('manager.dashboard') }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1>{{ $teamMember->name }}</h1>
            <p class="text-muted">{{ $teamMember->user->email }}</p>
        </div>
    </div>

    <div class="tabs-container">
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#clients">
                    Clients ({{ $clients->total() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#agreements">
                    Agreements ({{ $agreements->total() }})
                </a>
            </li>
        </ul>

        <div class="tab-content">
            <div id="clients" class="tab-pane fade show active">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Contact Person</th>
                                <th>Phone</th>
                                <th>Interest Status</th>
                                <th>Last Contact</th>
                                <th>Agreements</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($clients as $client)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <img src="{{ $client->company_logo }}" alt="{{ $client->company_name }}" class="company-logo-sm">
                                            {{ $client->company_name }}
                                        </div>
                                    </td>
                                    <td>{{ $client->contact_person }}</td>
                                    <td>{{ $client->phone }}</td>
                                    <td>
                                        <span class="badge badge-{{ $client->interest_status == 'interested' ? 'success' : ($client->interest_status == 'not interested' ? 'danger' : 'secondary') }}">
                                            {{ ucfirst($client->interest_status) }}
                                        </span>
                                    </td>
                                    <td>{{ $client->last_contact_date?->format('Y-m-d') }}</td>
                                    <td>{{ $client->agreements->count() }}</td>
                                    <td>
                                        <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No clients found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $clients->links() }}
            </div>

            <div id="agreements" class="tab-pane fade">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Client</th>
                                <th>Agreement Number</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Amount</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($agreements as $agreement)
                                <tr>
                                    <td>{{ $agreement->client->company_name }}</td>
                                    <td>{{ $agreement->agreement_number }}</td>
                                    <td>{{ $agreement->start_date?->format('Y-m-d') }}</td>
                                    <td>{{ $agreement->end_date?->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $agreement->agreement_status == 'active' ? 'success' : 'secondary' }}">
                                            {{ ucfirst($agreement->agreement_status) }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($agreement->agreement_amount ?? 0, 2) }}</td>
                                    <td>
                                        <a href="{{ route('agreements.show', $agreement) }}" class="btn btn-sm btn-outline-primary">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">No agreements found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $agreements->links() }}
            </div>
        </div>
    </div>
</div>

<style>
.company-logo-sm {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    object-fit: cover;
    margin-right: 0.5rem;
}

.tabs-container {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.nav-tabs {
    border-bottom: 2px solid #e9ecef;
}

.nav-tabs .nav-link {
    border: none;
    color: #6c757d;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
}

.nav-tabs .nav-link.active {
    color: #0d6efd;
    border-bottom: 2px solid #0d6efd;
}

.tab-content {
    padding-top: 1.5rem;
}
</style>
@endsection
