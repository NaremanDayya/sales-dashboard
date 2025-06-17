@extends('layouts.master')

@section('content')
<div class="pagetitle">
    <h1 class="text-gradient">
        @if($user->role == 'salesRep')
        الملف الشخصي لمندوب المبيعات
        @else
        الملف الشخصي للادمن
        @endif
    </h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li>
            <li class="breadcrumb-item active">الملف الشخصي</li>
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
                    @php
                    $roles = [
                    'salesRep' => 'مندوب مبيعات',
                    'admin' => 'المدير',
                    ];

                    $roleText = $roles[$user->role] ?? 'غير معروف';
                    @endphp
                    <p class="profile-title text-muted">{{ $roleText}}
                    </p>
                    <div class="profile-status mb-3">
                        @if($user->account_status === 'active')
                        <span class="badge bg-success rounded-pill px-3 py-1">نشط</span>
                        @elseif($user->account_status === 'inactive')
                        <span class="badge bg-secondary rounded-pill px-3 py-1">غير نشط</span>
                        @else
                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1">{{
                            ucfirst($user->account_status) ?? 'Unknown' }}</span>
                        @endif
                    </div>

                    @if($user->role === 'salesRep')
                    <div class="d-flex justify-content-center gap-3 mt-3">
                        <a href="{{ route('sales-reps.clients.index', $user->salesRep) }}"
                            class="btn btn-primary btn-sm">
                            <i class="bi bi-people-fill me-1"></i> عرض العملاء
                            @if(isset($user->salesRep->clients->count))
                            <span class="badge bg-light text-dark ms-1">{{ $user->salesRep->clients_count }}</span>
                            @endif
                        </a>
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
                            <h5 class="section-title"><i class="bi bi-person-lines-fill me-2"></i> المعلومات الشخصية
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-person-circle me-2"></i>الإسم الكامل</div>
                                <div class="info-value">{{ $user->name }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-at me-2"></i>إسم المستخدم</div>
                                <div class="info-value">{{ $user->username ?? '-' }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-envelope me-2"></i>البريد الإلكتروني</div>
                                <div class="info-value">{{ $user->email }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-shield-check me-2"></i>الدور الوظيفي</div>
                                @php
                                $roles = [
                                'salesRep' => 'مندوب مبيعات',
                                'admin' => 'المدير',
                                ];

                                $roleText = $roles[$user->role] ?? 'غير معروف';
                                @endphp

                                <div class="info-value">{{ $roleText }}</div>

                            </div>

                            @if($user->role === 'salesRep' && $user->salesRep)
                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-calendar-date me-2"></i>تاريخ الإلتحاق بالشركة
                                </div>
                                <div class="info-value">{{
                                    \Carbon\Carbon::parse($user->salesRep->start_work_date)->toFormattedDateString() }}
                                </div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-clock-history me-2"></i>مدة العمل</div>
                                <div class="info-value">{{
                                    $user->salesRep->translateDurationToArabic($user->salesRep->work_duration) }}</div>
                            </div>
                            @endif

                            @if(is_array($user->contact_info) && count($user->contact_info) > 0)
                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-telephone me-2"></i>رقم الجوال</div>
                                <div class="info-value">{{ $user->contact_info['phone'] ?? '-' }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-geo-alt me-2"></i>العنوان</div>
                                <div class="info-value">{{ $user->contact_info['address'] ?? '-' }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column - Main Content -->
                <div class="col-lg-8">
                    @if($user->role === 'salesRep')
                    <!-- Sales Rep Performance Dashboard -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-transparent border-0">
                            <h5 class="section-title"><i class="bi bi-speedometer2 me-2"></i> الأداء</h5>
                        </div>
                        <div class="card-body">
                            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                                <div class="col">
                                    <div class="card metric-card h-100">
                                        <div class="card-body text-center">
                                            <div class="metric-icon bg-primary-light">
                                                <i class="bi bi-bullseye text-primary"></i>
                                            </div>
                                            <h6 class="metric-title">العملاءالمستهدفين</h6>
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
                                            <h6 class="metric-title">العملاء المتأخرين</h6>
                                            <p class="metric-value text-danger">{{ $user->salesRep->lateCustomers }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card metric-card h-100">
                                        <div class="card-body text-center">
                                            <div class="metric-icon bg-success-light">
                                                <i class="bi bi-cart-check text-success"></i>
                                            </div>
                                            <h6 class="metric-title">الطلبات الكلية</h6>
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
                                            <h6 class="metric-title">الطلبات المعلقة</h6>
                                            <p class="metric-value text-warning">{{ $user->salesRep->totalPendedRequests
                                                }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card metric-card h-100">
                                        <div class="card-body text-center">
                                            <div class="metric-icon bg-info-light">
                                                <i class="bi bi-star-fill text-info"></i>
                                            </div>
                                            <h6 class="metric-title">العملاء المهتمين</h6>
                                            <p class="metric-value">{{ $user->salesRep->interestedClients->count() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card metric-card h-100">
                                        <div class="card-body text-center">
                                            <div class="metric-icon bg-primary-light">
                                                <i class="bi bi-file-earmark-text-fill text-primary"></i>
                                            </div>
                                            <h6 class="metric-title">عدد الاتفاقيات</h6>
                                            <p class="metric-value">{{ $user->salesRep->agreements->count() }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-4">
                                    <div class="card metric-card h-100">
                                        <div class="card-body text-center">
                                            <div class="metric-icon bg-info-light">
                                                <i class="bi bi-clipboard-data text-info"></i>
                                            </div>
                                            <h6 class="metric-title">هدف الشهر الحالي</h6>
                                            <p class="metric-value">
                                                {{ round($user->salesRep->currentMonthAchievedPercentage()) }}%
                                            </p>
                                            <small class="text-muted">
                                                الكمية المتحققة:
                                                {{ number_format($user->salesRep->currentMonthAchievedAmount()) }}/{{
                                                number_format($user->salesRep->currentMonthTargetAmount()) }}

                                            </small>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-4">
                                    <div class="card metric-card h-100">
                                        <div class="card-body text-center">
                                            <div class="metric-icon bg-success-light">
                                                <i class="bi bi-cash-coin text-success"></i>
                                            </div>
                                            <h6 class="metric-title">العمولات المستحقة</h6>
                                            <p class="metric-value">
                                                {{ number_format($user->salesRep->currentMonthAchievedCommission()) }}
                                                ر.س
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card metric-card h-100">
                                        <div class="card-body text-center">
                                            <div class="metric-icon bg-success-light">
                                                <i class="bi bi-graph-up-arrow text-success"></i>
                                            </div>
                                            <h6 class="metric-title">معدل القبول</h6>
                                            <p class="metric-value">
                                                @php
                                                $rate = $user->salesRep->totalOrders > 0
                                                ? round(($user->salesRep->totalOrders -
                                                $user->salesRep->totalPendedRequests) /
                                                $user->salesRep->totalOrders * 100)
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
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center mt-4">
                <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-lg px-4 me-3">
                    <i class="bi bi-pencil-square me-2"></i> تعديل الملف الشخصي

                @if($user->role === 'admin')
                <a href="#" class="btn btn-outline-danger btn-lg px-4" data-bs-toggle="modal"
                    data-bs-target="#deleteAccountModal">
                    <i class="bi bi-trash me-2"></i> حذف حساب المندوب
                </a>
                @endif
            </div>
        </div>
    </div>
</section>

@if($user->role === 'admin')
<!-- Delete Account Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">Confirm Account Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger" role="alert">
                    <h5 class="alert-heading"><i class="bi bi-exclamation-triangle-fill me-2"></i>Warning</h5>
                    <p>Deleting your account will permanently remove all your data. This action cannot be undone.</p>
                </div>
                <p>Are you sure you want to delete account?</p>
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

@if($user->role === 'salesRep')
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
