
@extends('layouts.master')

@section('content')
<div class="pagetitle text-center ">
    <h1 class="text-gradient mb-0">
        @if($user->role == 'salesRep')
        أهلا وسهلا بك سفير العلامة التجارية العزيز <span class="fw-bold" style="font-size: 1.2em; color: #4f46e5;">{{ $user->name
            }}</span> نتمنى لك عمل موفق ويوم جميل
        @else
        أهلا وسهلا بك أستاذ <span class="fw-bold" style="font-size: 1.2em; color: #4f46e5;">{{ $user->name
            }}</span> العزيز نتمنى لك عمل موفق ويوم جميل
        @endif
    </h1>
</div>
<section class="section profile">
    <div class="row justify-content-center">
        <div class="col-xl-10">
            <!-- Profile Header with Cover Photo -->
            <div class="card profile-header shadow-lg">
                <div class="cover-photo" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></div>
                <div class="profile-avatar">
                    <div class="avatar-edit-container text-center">
                        <div class="avatar-wrapper mb-2 position-relative">

<img src="{{ $user->personal_image
    ? Storage::disk('s3')->temporaryUrl($user->personal_image, now()->addMinutes(5))
    : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}"
                                 alt="صورة الملف الشخصي"
                                 class="rounded-circle"
                                 width="120"
                                 id="profileImage">

                                 </div>

@if (
    request()->is('showProfile') ||
    (request()->routeIs('sales-reps.show') && Auth::id() === $user->id)
)
    <form id="avatarUploadForm"
          action="{{ request()->is('showProfile')
              ? route('admin.updatePhoto')
              : route('sales-reps.updatePhoto', $user->salesRep) }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf
        <input type="file" id="profilePhotoInput" name="profile_photo_path"
               accept="image/jpeg,image/png,image/gif" style="display: none;">
        <button type="button" class="btn btn-sm btn-light avatar-edit-btn"
                onclick="document.getElementById('profilePhotoInput').click()">
            <i class="bi bi-pencil-fill"></i>
        </button>
    </form>
@endif
                    </div>
                </div>
                <div class="card-body pt-5 mt-4 text-center">
                    <h3 class="profile-name">{{ $user->name }}</h3>
                    @php
                    $roles = [
                    'salesRep' => 'سفير العلامة التجارية',
                    'admin' => 'المدير',
                    ];

                    $roleText = $roles[$user->role] ?? 'غير معروف';
                    @endphp
<p class="profile-title text-muted font-bold d-flex align-items-center justify-content-center gap-2">
    {{ $roleText }}
    @if(Auth::user()->role === 'admin' && isset($salesRep))
<div x-data="{ showAdminPasswordModal: false }">
    <!-- Trigger Button -->
    <button @click="showAdminPasswordModal = true" class="btn btn-primary">
        <i class="bi bi-key"></i> تغيير كلمة المرور
    </button>

      <div class="mt-2">
        <a href="{{ url('/admin/impersonate/' . $salesRep->user->id) }}"
           class="btn btn-warning">
            <i class="bi bi-person-check-fill"></i> الدخول كـ {{ $salesRep->user->name }}
        </a>
    </div>

    <!-- Modal -->
    <div x-show="showAdminPasswordModal"
        x-cloak
	x-transition.opacity
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div @click.away="showAdminPasswordModal = false"
             class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">تغيير كلمة مرور المدير</h3>
                <button @click="showAdminPasswordModal = false" class="text-gray-500 hover:text-gray-700">
                    <i class="bi bi-x-lg"></i>
                </button>

            </div>

            <form action="{{ route('admin.password.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    <div>
                        <label class="block mb-1">كلمة المرور الحالية</label>
                        <input type="password" name="current_password" required
                               class="w-full p-2 border rounded">
                    </div>

                    <div>
                        <label class="block mb-1">كلمة المرور الجديدة</label>
                        <input type="password" name="new_password" required
                               class="w-full p-2 border rounded">
                    </div>

                    <div>
                        <label class="block mb-1">تأكيد كلمة المرور</label>
                        <input type="password" name="new_password_confirmation" required
                               class="w-full p-2 border rounded">
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="showAdminPasswordModal = false"
                                class="px-4 py-2 border rounded hover:bg-gray-50">
                            إلغاء
                        </button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            حفظ التغييرات
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
    @endif
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
                                <div class="info-label"><i class="bi bi-person-circle me-2"></i>رقم الجوال </div>
                    <div class="info-value">{{ $user->contact_info['phone'] ?? '' }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-envelope me-2"></i>البريد الإلكتروني</div>
                                <div class="info-value">{{ $user->email }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-shield-check me-2"></i>الدور الوظيفي</div>
                                @php
                                $roles = [
                                'salesRep' => 'سفير العلامة التجارية',
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
<div class="info-item">
    <div class="info-label">
        <i class="bi bi-calendar-event me-2 text-blue-600"></i>تاريخ الميلاد
    </div>
    <div class="info-value">
        {{ \Carbon\Carbon::parse($user->birthday)->translatedFormat('d F Y') }} — {{ $user->getAge() }} عامًا
    </div>
</div>
@php
    $genderLabel = match($salesRep->user?->gender ?? '') {
        'male' => 'ذكر',
        'female' => 'أنثى',
        default => 'غير محدد',
    };
@endphp
                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-gender-ambiguous me-2"></i>الجنس</div>
<div class="info-value">{{ $genderLabel }}</div>
                            </div>

                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-credit-card-2-front me-2"></i>رقم الهوية</div>
                                <div class="info-value">{{ $user->id_card }}</div>
                            </div>
                            @endif

                            @if(is_array($user->contact_info) && count($user->contact_info) > 0)
                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-telephone me-2"></i>رقم الجوال</div>
                                <div class="info-value">{{ $user->contact_info['phone'] ?? '-' }}</div>
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
                            <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 g-4">
                                {{-- العملاء المستهدفين --}}
                                <div class="col">
                                    <div class="card metric-card h-100 text-center">
                                        <div class="card-body">
                                            <div class="metric-icon bg-primary-light">
                                                <i class="bi bi-bullseye text-primary"></i>
                                            </div>
<a href="{{ route('sales-reps.clients.index', $user->salesRep->id) }}" class="block no-underline text-gray-800 hover:text-blue-600">
    <h6 class="metric-title">العملاء المستهدفين</h6>
    <p class="metric-value">{{ $user->salesRep->clients->count() }}</p>
</a>
                                        </div>
                                    </div>
                                </div>{{-- العملاء المتأخرين --}}
                                <div class="col">
                                    <div class="card metric-card h-100 text-center">
                                        <div class="card-body">
                                            <div class="metric-icon bg-danger-light">
                                                <i class="bi bi-exclamation-triangle-fill text-danger"></i>
                                            </div>
<a href="{{ route('sales-reps.clients.index', $user->salesRep->id) }}?filter=late" class="block no-underline text-gray-800 hover:text-red-600">
    <h6 class="metric-title">العملاء المتأخرين</h6>
    <p class="metric-value text-danger">{{ $user->salesRep->lateCustomers }}</p>
</a>
                                        </div>
                                    </div>
                                </div>

                                {{-- الطلبات الكلية --}}
                                <div class="col">
                                    <div class="card metric-card h-100 text-center">
                                        <div class="card-body">
                                            <div class="metric-icon bg-success-light">
                                                <i class="bi bi-cart-check text-success"></i>
                                            </div>
<a href="{{ route('admin.allRequests') }}" class="block no-underline text-gray-800 hover:text-indigo-600">
    <h6 class="metric-title">الطلبات الكلية</h6>
    <p class="metric-value">{{ $user->salesRep->totalOrders }}</p>
</a>                                        </div>
                                    </div>
                                </div>

                                {{-- الطلبات المعلقة --}}
                                <div class="col">
                                    <div class="card metric-card h-100 text-center">
                                        <div class="card-body">
                                            <div class="metric-icon bg-warning-light">
                                                <i class="bi bi-hourglass-split text-warning"></i>
                                            </div>
<a href="{{ route('admin.allRequests') }}" class="block no-underline text-gray-800 hover:text-indigo-600">
<h6 class="metric-title">الطلبات المعلقة</h6>
                                            <p class="metric-value text-warning">{{ $user->salesRep->totalPendedRequests
                                                }}</p>
 </a>                                        </div>
                                    </div>
                                </div>

                                {{-- العملاء المهتمين --}}
                                <div class="col">
                                    <div class="card metric-card h-100 text-center">
                                        <div class="card-body">
                                            <div class="metric-icon bg-info-light">
                                                <i class="bi bi-star-fill text-info"></i>
                                            </div>
<a href="{{ route('sales-reps.clients.index', $user->salesRep->id) }}?filter=late" class="block no-underline text-gray-800 hover:text-red-600">
     <h6 class="metric-title">العملاء المهتمين</h6>
                                            <p class="metric-value">{{ $user->salesRep->interestedClients->count() }}
                                            </p>

</a>                                        </div>
                                    </div>
                                </div>

                                {{-- عدد الاتفاقيات --}}
                                <div class="col">
                                    <div class="card metric-card h-100 text-center">
                                        <div class="card-body">
                                            <div class="metric-icon bg-primary-light">
                                                <i class="bi bi-file-earmark-text-fill text-primary"></i>
                                            </div>
<a href="{{ route('salesrep.agreements.index', $user->salesRep->id) }}" class="block no-underline text-gray-800 hover:text-red-600">
     <h6 class="metric-title">عدد الاتفاقيات</h6>
                                            <p class="metric-value">{{ $user->salesRep->agreements->count() }}</p>


</a>                                        </div>
                                    </div>
                                </div>

                                {{-- العمولات المستحقة --}}
                                <div class="col">
                                    <div class="card metric-card h-100 text-center">
                                        <div class="card-body">
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

                                {{-- معدل القبول --}}
                                <div class="col">
                                    <div class="card metric-card h-100 text-center">
                                        <div class="card-body">
                                            <div class="metric-icon bg-success-light">
                                                <i class="bi bi-graph-up-arrow text-success"></i>
                                            </div>
                                            <h6 class="metric-title">معدل القبول</h6>
                                            @php
                                            $rate = $user->salesRep->totalOrders > 0
                                            ? round(($user->salesRep->totalOrders -
                                            $user->salesRep->totalPendedRequests) / $user->salesRep->totalOrders * 100)
                                            : 0;
                                            @endphp
                                            <p class="metric-value">{{ $rate }}%</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    @endif
@php
    use App\Models\Client;
    use App\Models\Agreement;
    use App\Models\ClientRequest;
    use App\Models\SalesRep;
    use App\Models\Target;
    use Carbon\Carbon;

$currentYear = Carbon::now()->year;
$currentMonth = Carbon::now()->month;

$achievedTargetsCount = Target::where('is_achieved', true)
    ->where('year', $currentYear)
    ->where('month', $currentMonth)
    ->count();
    $clientsCount = Client::count();
    $agreementsCount = Agreement::count();
    $clientRequestsCount = ClientRequest::count();
    $salesRepsCount = SalesRep::count();
    $pendingRequestsCount = ClientRequest::where('status', 'pending')->count();
@endphp
                    @if($user->role === 'admin')
<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mt-6 text-right">

<div class="bg-white p-6 rounded-2xl shadow flex items-center justify-between">
    <div>
        <a href="{{ route('allClients') }}" class="block text-gray-800 no-underline hover:underline transition">
            <p class="mb-1" style="font-size:14px; font-weight:700;">إجمالي العملاء</p>
            <h2 class="text-2xl font-bold">{{ $clientsCount }}</h2>
        </a>
    </div>
    <div class="bg-blue-100 text-blue-600 p-3 rounded-full">
        {{-- أيقونة مختلفة: أيقونة أشخاص بثلاث رؤوس --}}
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17 20h5v-2a4 4 0 00-3-3.87M2 20h5v-2a4 4 0 013-3.87M12 4a4 4 0 110 8a4 4 0 010-8z"/>
        </svg>
    </div>
</div>

<div class="bg-white p-6 rounded-2xl shadow flex items-center justify-between">
    <div>
        <a href="{{ route('allAgreements') }}" class="block text-gray-800 no-underline hover:underline transition">
            <p class="mb-1" style="font-size:14px; font-weight:700;">إجمالي الاتفاقيات</p>
            <h2 class="text-2xl font-bold text-gray-800">{{ $agreementsCount }}</h2>
        </a>
    </div>
    <div class="bg-green-100 text-green-600 p-3 rounded-full">
        {{-- أيقونة جديدة: رمز وثيقة (document icon) --}}
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 12l2 2l4-4M12 22a10 10 0 100-20 10 10 0 000 20z"/>
        </svg>
    </div>
</div>

    {{-- إجمالي الطلبات --}}
<div class="bg-white p-6 rounded-2xl shadow flex items-center justify-between">
    <div>
        <a href="/admin/allRequests" class="block text-gray-800 no-underline hover:underline transition">
            <p class="mb-1" style="font-size:14px; font-weight:700;">طلبات العملاء</p>
            <h2 class="text-2xl font-bold text-gray-800">{{ $clientRequestsCount }}</h2>
        </a>
    </div>
    <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full">
        {{-- أيقونة جديدة: رمز قائمة (list icon) --}}
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M3 7h18M3 12h18M3 17h18"/>
        </svg>
    </div>
</div>

    {{-- عدد المندوبين --}}
<div class="bg-white p-6 rounded-2xl shadow flex items-center justify-between">
    <div>
        <a href="{{ url('/sales-reps') }}" class="block text-gray-800 no-underline hover:underline transition">
            <p class="mb-1" style="font-size:14px; font-weight:700;">عدد مندوبي المبيعات</p>
            <h2 class="text-2xl font-bold text-gray-800">{{ $salesRepsCount }}</h2>
        </a>
    </div>
    <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full">
        {{-- أيقونة جديدة: رمز شخص مع إشارة ترس (ممثل المبيعات) --}}
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14v7m-6 0h12"/>
        </svg>
    </div>
</div>

    {{-- الأهداف المحققة --}}
    <div class="bg-white p-6 rounded-2xl shadow flex items-center justify-between">
        <div>
            <p class="text-sm text-gray-500 mb-1"  style="font-size:14px; font-weight:700;">أهداف الشهر المتحققة</p>
            <h2 class="text-2xl font-bold text-gray-800">{{ $achievedTargetsCount }}</h2>
        </div>
        <div class="bg-purple-100 text-purple-600 p-3 rounded-full">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0a9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

    {{-- الطلبات المعلقة --}}
<div class="bg-white p-6 rounded-2xl shadow flex items-center justify-between">
    <div>
        <a href="/admin/allRequests" class="block text-gray-800 no-underline hover:underline transition">
            <p class="mb-1" style="font-size:14px; font-weight:700;">الطلبات المعلقة</p>
            <h2 class="text-2xl font-bold text-gray-800">{{ $pendingRequestsCount }}</h2>
        </a>
    </div>
    <div class="bg-red-100 text-red-600 p-3 rounded-full">
        {{-- أيقونة جديدة: رمز علامة إلغاء (x) --}}
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"/>
        </svg>
    </div>
</div>
</div>

@endif

                </div>
            </div>
        </div>
</section>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap');

    :root {
        --font-primary: 'Tajawal', sans-serif;
        --font-secondary: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        --text-dark: #2c3e50;
        --text-muted: #7f8c8d;
        --text-light: #f8f9fa;
    }

    body {
        font-family: var(--font-primary);
        color: var(--text-dark);
        line-height: 1.6;
    }

    .pagetitle {
        padding: 20px 0;
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
        border-radius: 10px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        font-size: 1rem;
        font-weight: 500;
    }


    .text-gradient {
        background: linear-gradient(135deg, #121214 0%, #121113 100%);
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
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid white;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        position: relative;
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
        font-size: 1rem;
        font-weight: 1000;
        color: #0f1010;
        margin-bottom: 0.3rem;
    }

    .avatar-edit-container {
        position: relative;
        display: inline-block;
    }

    .avatar-wrapper {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        border: 4px solid white;
        overflow: hidden;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .avatar-edit-btn {
        position: absolute;
        top: -10px;
        right: -10px;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: white;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        border: 1px solid #e2e8f0;
        z-index: 3;
        transition: all 0.3s ease;
    }

    .avatar-edit-btn:hover {
        background-color: #f1f5f9;
        transform: translateY(-2px) scale(1.05);
    }

    .avatar-edit-btn[disabled] {
        opacity: 0.7;
        cursor: wait;
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    .bi-arrow-clockwise {
        animation: spin 1s linear infinite;
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
        font-weight: 500;
        color: #111213;
        margin-bottom: 0.5rem;
    }

    .metric-value {
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 0;
    }
</style>
<script>
    document.getElementById('profilePhotoInput').addEventListener('change', function() {
        if (this.files && this.files[0]) {
            // Client-side validation
            const file = this.files[0];
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];

            if (!validTypes.includes(file.type)) {
                alert('الرجاء اختيار صورة بصيغة JPEG أو PNG أو GIF');
                return;
            }

            if (file.size > 2048 * 1024) { // 2MB
                alert('حجم الملف يجب أن لا يتجاوز 2MB');
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profileImage').src = e.target.result;
            }
            reader.readAsDataURL(file);

            // Submit form
            document.getElementById('avatarUploadForm').submit();

            // Show loading state
            const editBtn = document.querySelector('.avatar-edit-btn');
            editBtn.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
            editBtn.disabled = true;
        }
    });
</script>
@endsection

