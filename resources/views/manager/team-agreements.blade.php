@extends('layouts.master')

@section('content')
<div class="app-content">
    <div class="section-header">
        <div>
            <a href="{{ route('manager.dashboard', request()->only('manager_id')) }}" class="btn btn-sm btn-outline-secondary mb-2">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
            <h1>Team Agreements</h1>
            <p class="text-muted">All agreements managed by your team</p>
        </div>
    </div>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Sales Rep</th>
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
                            <td>{{ $agreement->salesRep->user->name ?? 'N/A' }}</td>
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
                            <td colspan="8" class="text-center text-muted">No agreements found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $agreements->links() }}
    </div>
</div>

<style>
.content-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}
</style>
@endsection
