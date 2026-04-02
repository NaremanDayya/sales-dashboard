@extends('layouts.master')

@section('content')
<div class="app-content">
    <div class="section-header">
        <h1>Manager Dashboard</h1>
        @if(auth()->user()->isImpersonatingManager())
            <div class="impersonation-badge">
                <span class="badge badge-warning">Viewing as {{ $salesRep->name }}</span>
                <form action="{{ route('admin.impersonation.stop') }}" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Exit View</button>
                </form>
            </div>
        @endif
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $teamStats['total_members'] }}</h3>
                <p>Team Members</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-success">
                <i class="fas fa-building"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $teamStats['total_clients'] }}</h3>
                <p>Total Clients</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-info">
                <i class="fas fa-file-contract"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $teamStats['total_agreements'] }}</h3>
                <p>Total Agreements</p>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon bg-warning">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $teamStats['active_agreements'] }}</h3>
                <p>Active Agreements</p>
            </div>
        </div>
    </div>

    <div class="content-section">
        <div class="section-title">
            <h2>Your Team</h2>
            <div class="section-actions">
                <a href="{{ route('manager.team.clients') }}" class="btn btn-outline-primary">View All Clients</a>
                <a href="{{ route('manager.team.agreements') }}" class="btn btn-outline-primary">View All Agreements</a>
            </div>
        </div>

        <div class="content-card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Clients</th>
                            <th>Agreements</th>
                            <th>Active Agreements</th>
                            <th>Start Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teamMembers as $member)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $member->user->personal_image }}" alt="{{ $member->name }}" class="member-avatar-sm me-2">
                                        <strong>{{ $member->name }}</strong>
                                    </div>
                                </td>
                                <td>{{ $member->user->email }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $member->clients->count() }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $member->agreements->count() }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success">{{ $member->active_agreements_count }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($member->start_work_date)->format('Y-m-d') }}</td>
                                <td>
                                    <a href="{{ route('manager.team-member.details', $member) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                    <p>No team members assigned yet.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-content h3 {
    margin: 0;
    font-size: 2rem;
    font-weight: 700;
}

.stat-content p {
    margin: 0;
    color: #6c757d;
    font-size: 0.875rem;
}

.section-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.section-actions {
    display: flex;
    gap: 0.5rem;
}

.content-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.member-avatar-sm {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.impersonation-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
</style>
@endsection
