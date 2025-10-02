@extends('layouts.master')

@section('content')
<div class="pagetitle">
    <h1 class="text-gradient">
        @if($user->role === 'salesRep')
            Sales Representative Profile
        @else
            My Profile
        @endif
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Profile</li>
        </ol>
    </nav>
</div>

<section class="section profile">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <!-- Profile Header with Cover Photo -->
            <div class="card profile-header shadow-lg">
                <div class="cover-photo" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                <div class="profile-avatar">
                    <div class="avatar-wrapper">
                        <img src="{{ $user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=random' }}"
                             alt="Profile" class="rounded-circle">
                    </div>
                </div>
                <div class="card-body pt-5 mt-4 text-center">
                    <h3 class="profile-name">{{ $user->name }}</h3>
                    <p class="profile-title text-muted">{{ ucfirst(str_replace('_', ' ', $user->role)) ?? 'Member' }}</p>
                    <div class="profile-status mb-3">
                        @if($user->account_status === 'active')
                        <span class="badge bg-success rounded-pill px-3 py-1">Active</span>
                        @elseif($user->account_status === 'inactive')
                        <span class="badge bg-secondary rounded-pill px-3 py-1">Inactive</span>
                        @else
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1">{{ ucfirst($user->account_status) ?? 'Unknown' }}</span>
                        @endif
                    </div>

                    @if($user->role === 'sales_rep')
                    <div class="d-flex justify-content-center gap-3 mt-3">
                        <a href="{{ route('sales-reps.clients.index', $user->salesRep) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-people-fill me-1"></i> View Clients
                            @if(isset($user->salesRep->clients_count))
                            <span class="badge bg-light text-dark ms-1">{{ $user->salesRep->clients_count }}</span>
                            @endif
                        </a>
                        <button id="generateReportBtn" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-file-earmark-pdf me-1"></i> Generate Report
                            <span id="spinner" class="spinner-border spinner-border-sm d-none ms-1"></span>
                        </button>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Main Profile Content -->
            <div class="row mt-4">
                <!-- Left Column - Personal Info -->
                <div class="col-lg-4">
                    <!-- Profile Details Card -->
                    <div class="card profile-details shadow-sm h-100">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="section-title"><i class="bi bi-person-lines-fill me-2"></i> Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-person-circle me-2"></i>Full Name</div>
                                <div class="info-value">{{ $user->name }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-at me-2"></i>Username</div>
                                <div class="info-value">{{ $user->username ?? '-' }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-envelope me-2"></i>Email</div>
                                <div class="info-value">{{ $user->email }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-shield-check me-2"></i>Role</div>
                                <div class="info-value text-capitalize">{{ str_replace('_', ' ', $user->role) ?? 'N/A' }}</div>
                            </div>

                            @if($user->role === 'sales_rep' && $user->salesRep)
                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-calendar-date me-2"></i>Start Date</div>
                                <div class="info-value">{{ \Carbon\Carbon::parse($user->salesRep->start_work_date)->toFormattedDateString() }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-clock-history me-2"></i>Work Duration</div>
                                <div class="info-value">{{ $user->salesRep->work_duration }}</div>
                            </div>
                            @endif

                            @if(is_array($user->contact_info) && count($user->contact_info) > 0)
                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-telephone me-2"></i>Phone</div>
                                <div class="info-value">{{ $user->contact_info['phone'] ?? '-' }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Main Content -->
                <div class="col-lg-8">
                    @if($user->role === 'sales_rep' && $user->salesRep)
                    <!-- Sales Rep Performance Dashboard -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="section-title"><i class="bi bi-speedometer2 me-2"></i> Performance Dashboard</h5>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                <div class="col">
                                    <div class="card metric-card h-100">
                                        <div class="card-body text-center">
                                            <div class="metric-icon bg-primary-light">
                                                <i class="bi bi-bullseye text-primary"></i>
                                            </div>
                                            <h6 class="metric-title">Targeted Customers</h6>
                                            <p class="metric-value">{{ $user->salesRep->clients->count() }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card metric-card h-100">
                                        <div class="card-body text-center">
                                            <div class="metric-icon bg-danger-light">
                                                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                                            </div>
                                            <h6 class="metric-title">Late Customers</h6>
                                            <p class="metric-value text-danger">{{ $user->salesRep->late_customers }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card metric-card h-100">
                                        <div class="card-body text-center">
                                            <div class="metric-icon bg-success-light">
                                                <i class="bi bi-cart-check text-success"></i>
                                            </div>
                                            <h6 class="metric-title">Total Orders</h6>
                                            <p class="metric-value">{{ $user->salesRep->totalOrders }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card metric-card h-100">
                                        <div class="card-body text-center">
                                            <div class="metric-icon bg-warning-light">
                                                <i class="bi bi-hourglass-split text-warning"></i>
                                            </div>
                                            <h6 class="metric-title">Pending Orders</h6>
                                            <p class="metric-value text-warning">{{ $user->salesRep->pendedRequest->count() }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card metric-card h-100">
                                        <div class="card-body text-center">
                                            <div class="metric-icon bg-info-light">
                                                <i class="bi bi-star-fill text-info"></i>
                                            </div>
                                            <h6 class="metric-title">Interested Customers</h6>
                                            <p class="metric-value">{{ $user->salesRep->interestedClients->count() }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card metric-card h-100">
                                        <div class="card-body text-center">
                                            <div class="metric-icon bg-success-light">
                                                <i class="bi bi-graph-up-arrow text-success"></i>
                                            </div>
                                            <h6 class="metric-title">Success Rate</h6>
                                            <p class="metric-value">
                                                @php
                                                $rate = $user->salesRep->total_orders > 0
                                                ? round(($user->salesRep->total_orders - $user->salesRep->pending_orders) /
                                                  $user->salesRep->total_orders * 100)
                                                : 0;
                                                @endphp
                                                {{ $rate }}%
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Additional Information Cards -->
                    <div class="row">
                        <!-- Privileges Card -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-transparent border-0">
                                    <h5 class="section-title"><i class="bi bi-stars me-2"></i> Privileges</h5>
                                </div>
                                <div class="card-body">
                                    @if($user->privileges)
                                    <ul class="privilege-list">
                                        @foreach((array) json_decode($user->privileges) ?: [] as $privilege)
                                        <li class="privilege-item">
                                            <i class="bi bi-check-circle-fill text-success me-2"></i>
                                            <span>{{ $privilege }}</span>
                                        </li>
                                        @endforeach
                                    </ul>
                                    @else
                                    <div class="text-muted">No special privileges assigned</div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Contact Info Card -->
                        <div class="col-md-6 mb-4">
                            <div class="card h-100 shadow-sm">
                                <div class="card-header bg-transparent border-0">
                                    <h5 class="section-title"><i class="bi bi-info-circle me-2"></i> Additional Info</h5>
                                </div>
                                <div class="card-body">
                                    @if(is_array($user->contact_info) && count($user->contact_info) > 0)
                                    <ul class="info-list">
                                        @foreach($user->contact_info as $key => $value)
                                            @if(!in_array($key, ['phone', 'address']))
                                            <li class="info-item">
                                                <span class="info-key">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                <span class="info-value">{{ $value }}</span>
                                            </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                    @else
                                    <div class="text-muted">No additional information provided</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-lg px-4 me-3">
                    <i class="bi bi-pencil-square me-2"></i> Edit Profile
                </a>

                @if($user->role === 'admin')
                <a href="#" class="btn btn-outline-danger btn-lg px-4" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                    <i class="bi bi-trash me-2"></i> Delete Account
                </a>
                @endif
            </div>
        </div>
    </div>
</section>

@if($user->role === 'admin')
<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">Confirm Account Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                    <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Warning</h5>
                    <p>Deleting your account will permanently remove all your data. This action cannot be undone.</p>
                </div>
                <p>Are you sure you want to delete your account?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('profile.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Account</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endif

@if($user->role === 'sales_rep')
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const generateBtn = document.getElementById('generateReportBtn');
        const spinner = document.getElementById('spinner');

        if (generateBtn) {
            generateBtn.addEventListener('click', function() {
                spinner.classList.remove('d-none');
                generateBtn.disabled = true;

                fetch(`{{ route('sales-reps.generate-report', $user->salesRep->id) }}`)
                    .then(response => {
                        if (!response.ok) throw new Error('Network error');
                        return response.blob();
                    })
                    .then(blob => {
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `SalesRep_Report_${new Date().toISOString().slice(0,10)}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        URL.revokeObjectURL(url);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        toastr.error('Failed to generate report');
                    })
                    .finally(() => {
                        spinner.classList.add('d-none');
                        generateBtn.disabled = false;
                    });
            });
        }
    });
</script>
@endpush
@endif

<style>
    /* Custom Styles for Modern Profile */
    .text-gradient {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        background-clip: text;
        -webkit-text-fill-color: transparent;
        display: inline-block;
    }

    .profile-header {
        position: relative;
        border-radius: 15px;
        overflow: hidden;
        border: none;
    }

    .cover-photo {
        height: 150px;
        width: 100%;
        background-size: cover;
        background-position: center;
    }

    .profile-avatar {
        position: absolute;
        top: 100px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2;
    }

    .avatar-wrapper {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 4px solid white;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .avatar-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-name {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 5px;
    }

    .profile-title {
        font-size: 0.9rem;
    }

    .profile-details {
        border-radius: 15px;
        border: none;
    }

    .section-title {
        font-weight: 600;
        color: #2c3e50;
        font-size: 1.1rem;
    }

    .info-item {
        margin-bottom: 1.5rem;
    }

    .info-label {
        font-size: 0.85rem;
        color: #7f8c8d;
        margin-bottom: 0.3rem;
    }

    .info-value {
        font-weight: 500;
        color: #2c3e50;
        font-size: 1rem;
    }

    .privilege-list,
    .info-list {
        list-style: none;
        padding-left: 0;
    }

    .privilege-item,
    .info-item {
        padding: 0.5rem 0;
        border-bottom: 1px solid #f1f1f1;
    }

    .privilege-item:last-child,
    .info-item:last-child {
        border-bottom: none;
    }

    .info-key {
        font-weight: 500;
        color: #7f8c8d;
        margin-right: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 50px;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        transform: translateY(-2px);
    }

    /* Sales Rep Specific Styles */
    .metric-card {
        border-radius: 10px;
        transition: transform 0.3s ease;
    }

    .metric-card:hover {
        transform: translateY(-5px);
    }

    .metric-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 1.25rem;
    }

    .bg-primary-light {
        background-color: rgba(102, 126, 234, 0.1);
    }

    .bg-danger-light {
        background-color: rgba(220, 53, 69, 0.1);
    }

    .bg-success-light {
        background-color: rgba(25, 135, 84, 0.1);
    }

    .bg-warning-light {
        background-color: rgba(255, 193, 7, 0.1);
    }

    .bg-info-light {
        background-color: rgba(13, 202, 240, 0.1);
    }

    .metric-title {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 0.5rem;
    }

    .metric-value {
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 0;
    }
</style>
@endsection
