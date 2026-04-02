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

        <div class="team-grid">
            @forelse($teamMembers as $member)
                <div class="team-member-card">
                    <div class="member-header">
                        <img src="{{ $member->user->personal_image }}" alt="{{ $member->name }}" class="member-avatar">
                        <div class="member-info">
                            <h3>{{ $member->name }}</h3>
                            <p class="text-muted">{{ $member->user->email }}</p>
                        </div>
                    </div>
                    
                    <div class="member-stats">
                        <div class="stat-item">
                            <span class="stat-label">Clients</span>
                            <span class="stat-value">{{ $member->clients->count() }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Agreements</span>
                            <span class="stat-value">{{ $member->agreements->count() }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Active</span>
                            <span class="stat-value">{{ $member->active_agreements_count }}</span>
                        </div>
                    </div>

                    <div class="member-actions">
                        <a href="{{ route('manager.team-member.details', $member) }}" class="btn btn-sm btn-primary">
                            View Details
                        </a>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <i class="fas fa-users fa-3x text-muted"></i>
                    <p>No team members assigned yet.</p>
                </div>
            @endforelse
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

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1.5rem;
}

.team-member-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.member-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.member-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}

.member-info h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
}

.member-info p {
    margin: 0;
    font-size: 0.875rem;
}

.member-stats {
    display: flex;
    justify-content: space-around;
    padding: 1rem 0;
    border-top: 1px solid #e9ecef;
    border-bottom: 1px solid #e9ecef;
    margin-bottom: 1rem;
}

.stat-item {
    text-align: center;
}

.stat-label {
    display: block;
    font-size: 0.75rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
}

.stat-value {
    display: block;
    font-size: 1.25rem;
    font-weight: 600;
}

.member-actions {
    display: flex;
    gap: 0.5rem;
}

.impersonation-badge {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 3rem;
    color: #6c757d;
}
</style>
@endsection
