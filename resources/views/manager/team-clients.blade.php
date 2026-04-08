@extends('layouts.master')

@section('content')
<div class="app-content">
    <div class="section-header">
        <div>
            <a href="{{ route('manager.dashboard', request()->only('manager_id')) }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1>Team Clients</h1>
            <p class="text-muted">All clients managed by your team</p>
        </div>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Company</th>
                        <th>Sales Rep</th>
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
                            <td>{{ $client->salesRep->user->name ?? 'N/A' }}</td>
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
                                <a href="{{ route('clients.show.manager', $client) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No clients found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $clients->links() }}
    </div>
</div>

<style>
.content-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.company-logo-sm {
    width: 32px;
    height: 32px;
    border-radius: 4px;
    object-fit: cover;
    margin-right: 0.5rem;
}
</style>
@endsection
