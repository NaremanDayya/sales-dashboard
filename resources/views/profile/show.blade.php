@extends('layouts.master')

@section('content')
<x-page-header>
    <x-slot name="title">
        @if($user->role == 'salesRep')
            أهلا وسهلا بك سفير العلامة التجارية العزيز <span class="text-indigo-600">{{ $user->name }}</span> نتمنى لك عمل موفق ويوم جميل
        @else
            أهلا وسهلا بك أستاذ <span class="text-indigo-600">{{ $user->name }}</span> العزيز نتمنى لك عمل موفق ويوم جميل
        @endif
    </x-slot>
</x-page-header>

<div class="max-w-6xl mx-auto">
    <!-- Profile Header Card -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="h-32 bg-gradient-to-br from-indigo-500 to-purple-600"></div>
        <div class="px-6 pb-6">
            <div class="flex flex-col items-center -mt-16">
                <div class="relative">
                    <div class="h-28 w-28 rounded-full border-4 border-white shadow overflow-hidden bg-gray-100">
                        <img src="{{ $user->personal_image }}" alt="صورة الملف الشخصي" class="h-full w-full object-cover" id="profileImage">
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
                            <button type="button"
                                    class="avatar-edit-btn absolute -top-1 -end-1 h-8 w-8 rounded-full bg-white border border-gray-200 shadow flex items-center justify-center text-gray-500 hover:bg-gray-50 transition-colors"
                                    onclick="document.getElementById('profilePhotoInput').click()">
                                <i class="bi bi-pencil-fill text-sm"></i>
                            </button>
                        </form>
                    @endif
                </div>

                <h3 class="mt-3 text-lg font-semibold text-gray-900">{{ $user->name }}</h3>

                @php
                    $roles = [
                        'salesRep' => 'سفير العلامة التجارية',
                        'admin' => 'المدير',
                    ];
                    $roleText = $roles[$user->role] ?? 'غير معروف';
                @endphp

                <div class="mt-1 flex flex-wrap items-center justify-center gap-2 text-sm text-gray-500 font-medium">
                    {{ $roleText }}

                    @if(Auth::user()->role === 'admin' && isset($salesRep))
                        <div x-data="{ showSalesRepPasswordModal: false }" class="flex items-center gap-2">
                            <button @click="showSalesRepPasswordModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition-colors">
                                <i class="bi bi-key"></i> تغيير كلمة المرور
                            </button>

                            <a href="{{ url('/admin/impersonate/' . $salesRep->user->id) }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-500 text-white text-xs font-semibold hover:bg-amber-600 transition-colors">
                                <i class="bi bi-person-check-fill"></i> الدخول كـ {{ $salesRep->user->name }}
                            </a>

                            <div x-show="showSalesRepPasswordModal" x-cloak x-transition.opacity
                                 class="fixed inset-0 bg-gray-900/50 flex items-center justify-center z-50 px-4">
                                <div @click.away="showSalesRepPasswordModal = false" class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md text-start">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-base font-semibold text-gray-900">تغيير كلمة مرور {{ $salesRep->user->name }}</h3>
                                        <button @click="showSalesRepPasswordModal = false" class="text-gray-400 hover:text-gray-600">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>

                                    <form id="salesRepPasswordForm" action="{{ route('salesrep.password.change', ['salesrep' => $salesRep->id]) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="space-y-4">
                                            <div>
                                                <label class="block mb-1.5 text-sm font-medium text-gray-700">كلمة المرور الجديدة</label>
                                                <div class="relative">
                                                    <input type="password" id="salesrepNewPassword" name="salesrepPassword" required minlength="8"
                                                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pe-10">
                                                    <button type="button" onclick="togglePasswordVisibilityProfile('salesrepNewPassword', 'toggleIconNewProfile')"
                                                        class="absolute inset-y-0 start-3 flex items-center text-gray-400 hover:text-gray-600">
                                                        <i id="toggleIconNewProfile" class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                                <small class="text-gray-400">يجب أن تكون كلمة المرور 8 أحرف على الأقل</small>
                                            </div>

                                            <div>
                                                <label class="block mb-1.5 text-sm font-medium text-gray-700">تأكيد كلمة المرور</label>
                                                <div class="relative">
                                                    <input type="password" id="salesrepConfirmPassword" name="salesrepPassword_confirmation" required minlength="8"
                                                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pe-10">
                                                    <button type="button" onclick="togglePasswordVisibilityProfile('salesrepConfirmPassword', 'toggleIconConfirmProfile')"
                                                        class="absolute inset-y-0 start-3 flex items-center text-gray-400 hover:text-gray-600">
                                                        <i id="toggleIconConfirmProfile" class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="flex justify-end gap-2 pt-2">
                                                <button type="button" @click="showSalesRepPasswordModal = false"
                                                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                                    إلغاء
                                                </button>
                                                <button type="submit"
                                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                                    حفظ كلمة المرور
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(Auth::user()->role === 'admin' && !isset($salesRep))
                        <div x-data="{ showAdminPasswordModal: false }">
                            <button @click="showAdminPasswordModal = true" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition-colors">
                                <i class="bi bi-key"></i> تغيير كلمة المرور
                            </button>

                            <div x-show="showAdminPasswordModal" x-cloak x-transition.opacity
                                 class="fixed inset-0 bg-gray-900/50 flex items-center justify-center z-50 px-4">
                                <div @click.away="showAdminPasswordModal = false" class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md text-start">
                                    <div class="flex justify-between items-center mb-4">
                                        <h3 class="text-base font-semibold text-gray-900">تغيير كلمة مرور المدير</h3>
                                        <button @click="showAdminPasswordModal = false" class="text-gray-400 hover:text-gray-600">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>

                                    <form action="{{ route('admin.password.update') }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <div class="space-y-4">
                                            <div>
                                                <label class="block mb-1.5 text-sm font-medium text-gray-700">كلمة المرور الحالية</label>
                                                <div class="relative">
                                                    <input type="password" id="adminCurrentPassword" name="current_password" required
                                                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pe-10">
                                                    <button type="button" onclick="togglePasswordVisibilityProfile('adminCurrentPassword', 'toggleIconCurrentAdmin')"
                                                        class="absolute inset-y-0 start-3 flex items-center text-gray-400 hover:text-gray-600">
                                                        <i id="toggleIconCurrentAdmin" class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block mb-1.5 text-sm font-medium text-gray-700">كلمة المرور الجديدة</label>
                                                <div class="relative">
                                                    <input type="password" id="adminNewPassword" name="new_password" required
                                                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pe-10">
                                                    <button type="button" onclick="togglePasswordVisibilityProfile('adminNewPassword', 'toggleIconNewAdmin')"
                                                        class="absolute inset-y-0 start-3 flex items-center text-gray-400 hover:text-gray-600">
                                                        <i id="toggleIconNewAdmin" class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block mb-1.5 text-sm font-medium text-gray-700">تأكيد كلمة المرور</label>
                                                <div class="relative">
                                                    <input type="password" id="adminConfirmPassword" name="new_password_confirmation" required
                                                           class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm pe-10">
                                                    <button type="button" onclick="togglePasswordVisibilityProfile('adminConfirmPassword', 'toggleIconConfirmAdmin')"
                                                        class="absolute inset-y-0 start-3 flex items-center text-gray-400 hover:text-gray-600">
                                                        <i id="toggleIconConfirmAdmin" class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="flex justify-end gap-2 pt-2">
                                                <button type="button" @click="showAdminPasswordModal = false"
                                                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                                    إلغاء
                                                </button>
                                                <button type="submit"
                                                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                                    حفظ التغييرات
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-3">
                    @if($user->account_status === 'active')
                        <x-badge color="emerald">نشط</x-badge>
                    @elseif($user->account_status === 'inactive')
                        <x-badge color="gray">غير نشط</x-badge>
                    @else
                        <x-badge color="amber">{{ ucfirst($user->account_status) ?? 'Unknown' }}</x-badge>
                    @endif
                </div>

                @if($user->role === 'salesRep')
                    <div class="mt-4">
                        <a href="{{ route('sales-reps.clients.index', $user->salesRep) }}"
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-sm font-medium hover:bg-indigo-700 transition-colors">
                            <i class="bi bi-people-fill"></i> عرض العملاء
                            @if(isset($user->salesRep->clients->count))
                                <span class="bg-white/20 rounded-full px-1.5 text-xs">{{ $user->salesRep->clients_count }}</span>
                            @endif
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Personal Info -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm h-full">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h5 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                        <i class="bi bi-person-lines-fill text-indigo-500"></i> المعلومات الشخصية
                    </h5>
                </div>
                <div class="px-5 py-4 divide-y divide-gray-100">
                    <div class="py-3 first:pt-0">
                        <div class="text-xs font-medium text-gray-400 flex items-center gap-1.5 mb-1"><i class="bi bi-person-circle"></i>الإسم الكامل</div>
                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                    </div>
                    <div class="py-3">
                        <div class="text-xs font-medium text-gray-400 flex items-center gap-1.5 mb-1"><i class="bi bi-telephone"></i>رقم الجوال</div>
                        <div class="text-sm font-medium text-gray-900">{{ $user->contact_info['phone'] ?? '' }}</div>
                    </div>
                    <div class="py-3">
                        <div class="text-xs font-medium text-gray-400 flex items-center gap-1.5 mb-1"><i class="bi bi-envelope"></i>البريد الإلكتروني</div>
                        <div class="text-sm font-medium text-gray-900">{{ $user->email }}</div>
                    </div>
                    <div class="py-3">
                        <div class="text-xs font-medium text-gray-400 flex items-center gap-1.5 mb-1"><i class="bi bi-shield-check"></i>الدور الوظيفي</div>
                        @php
                            $roles = ['salesRep' => 'سفير العلامة التجارية', 'admin' => 'المدير'];
                            $roleText = $roles[$user->role] ?? 'غير معروف';
                        @endphp
                        <div class="text-sm font-medium text-gray-900">{{ $roleText }}</div>
                    </div>

                    @if($user->role === 'salesRep' && $user->salesRep)
                        <div class="py-3">
                            <div class="text-xs font-medium text-gray-400 flex items-center gap-1.5 mb-1"><i class="bi bi-calendar-date"></i>تاريخ الإلتحاق بالشركة</div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($user->salesRep->start_work_date)->locale('ar')->isoFormat('D MMMM YYYY') }}
                            </div>
                        </div>

                        <div class="py-3">
                            <div class="text-xs font-medium text-gray-400 flex items-center gap-1.5 mb-1"><i class="bi bi-clock-history"></i>مدة العمل</div>
                            <div class="text-sm font-medium text-gray-900">{{ $user->salesRep->translateDurationToArabic($user->salesRep->work_duration) }}</div>
                        </div>
                        <div class="py-3">
                            <div class="text-xs font-medium text-gray-400 flex items-center gap-1.5 mb-1"><i class="bi bi-calendar-event"></i>تاريخ الميلاد</div>
                            <div class="text-sm font-medium text-gray-900">
                                {{ \Carbon\Carbon::parse($user->birthday)->locale('ar')->isoFormat('D MMMM YYYY') }} — {{ $user->getAge() }} عامًا
                            </div>
                        </div>
                        @php
                            $genderLabel = match($salesRep->user?->gender ?? '') {
                                'male' => 'ذكر',
                                'female' => 'أنثى',
                                default => 'غير محدد',
                            };
                        @endphp
                        <div class="py-3">
                            <div class="text-xs font-medium text-gray-400 flex items-center gap-1.5 mb-1"><i class="bi bi-gender-ambiguous"></i>الجنس</div>
                            <div class="text-sm font-medium text-gray-900">{{ $genderLabel }}</div>
                        </div>

                        <div class="py-3">
                            <div class="text-xs font-medium text-gray-400 flex items-center gap-1.5 mb-1"><i class="bi bi-credit-card-2-front"></i>رقم الهوية</div>
                            <div class="text-sm font-medium text-gray-900">{{ $user->id_card }}</div>
                        </div>

                        <div class="py-3">
                            <div class="text-xs font-medium text-gray-400 flex items-center gap-1.5 mb-1"><i class="bi bi-person-badge"></i>المدير المباشر</div>
                            <div class="text-sm font-medium text-gray-900">
                                @if($user->salesRep->manager)
                                    <div class="flex items-center justify-between">
                                        <span>{{ $user->salesRep->manager->name }}</span>
                                        @if(Auth::user()->role === 'admin')
                                            <button class="text-red-500 hover:text-red-700 p-1" data-bs-toggle="modal" data-bs-target="#removeManagerModal">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-gray-400">لا يوجد مدير مباشر</span>
                                @endif
                            </div>
                            @if(Auth::user()->role === 'admin')
                                <button class="mt-2 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-medium hover:bg-indigo-700 transition-colors" data-bs-toggle="modal" data-bs-target="#assignManagerModal">
                                    <i class="bi bi-person-plus"></i> {{ $user->salesRep->manager ? 'تغيير المدير' : 'تعيين مدير' }}
                                </button>
                            @endif
                        </div>
                    @endif

                    @if($user->salesRep->isManager())
                        <div class="py-3">
                            <div class="text-xs font-medium text-gray-400 flex items-center gap-1.5 mb-1"><i class="bi bi-people"></i>أعضاء الفريق</div>
                            <div class="text-sm font-medium text-gray-900 flex items-center gap-2">
                                {{ $user->salesRep->teamMembers->count() }}
                                @if(Auth::id() === $user->id)
                                    <a href="{{ route('manager.dashboard') }}" class="text-xs font-medium text-indigo-600 hover:underline">عرض الفريق</a>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if(is_array($user->contact_info) && count($user->contact_info) > 0)
                        <div class="py-3 last:pb-0">
                            <div class="text-xs font-medium text-gray-400 flex items-center gap-1.5 mb-1"><i class="bi bi-telephone"></i>رقم الجوال</div>
                            <div class="text-sm font-medium text-gray-900">{{ $user->contact_info['phone'] ?? '-' }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="lg:col-span-2 space-y-6">
            @if($user->role === 'salesRep')
                <div class="bg-white border border-gray-200 rounded-2xl shadow-sm">
                    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h5 class="text-sm font-semibold text-gray-900 flex items-center gap-2">
                            <i class="bi bi-speedometer2 text-indigo-500"></i> الأداء
                        </h5>
                        @if(Auth::user()->role === 'admin')
                            <button class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-600 text-white text-xs font-medium hover:bg-indigo-700 transition-colors"
                                    data-bs-toggle="modal" data-bs-target="#assignManagerModal">
                                <i class="bi bi-person-plus"></i>
                                {{ $user->salesRep->manager ? 'تغيير المدير' : 'تعيين مدير' }}
                            </button>
                        @endif
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            <a href="{{ route('sales-reps.clients.index', $user->salesRep->id) }}" class="rounded-xl border border-gray-100 p-4 text-center hover:shadow-md hover:-translate-y-0.5 transition-all">
                                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 mb-2"><i class="bi bi-bullseye"></i></span>
                                <p class="text-xs font-medium text-gray-500">العملاء المستهدفين</p>
                                <p class="text-xl font-semibold text-gray-900">{{ $user->salesRep->clients->count() }}</p>
                            </a>

                            <a href="{{ route('sales-reps.clients.index', $user->salesRep->id) }}?interest_status=late" class="rounded-xl border border-gray-100 p-4 text-center hover:shadow-md hover:-translate-y-0.5 transition-all">
                                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-rose-50 text-rose-600 mb-2"><i class="bi bi-exclamation-triangle-fill"></i></span>
                                <p class="text-xs font-medium text-gray-500">العملاء المتأخرين</p>
                                <p class="text-xl font-semibold text-rose-600">{{ $user->salesRep->lateCustomers('interested')+$user->salesRep->lateCustomers('not interested')+$user->salesRep->lateCustomers('neutral') }}</p>
                            </a>

                            <a href="{{ route('admin.allRequests') }}" class="rounded-xl border border-gray-100 p-4 text-center hover:shadow-md hover:-translate-y-0.5 transition-all">
                                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 mb-2"><i class="bi bi-cart-check"></i></span>
                                <p class="text-xs font-medium text-gray-500">الطلبات الكلية</p>
                                <p class="text-xl font-semibold text-gray-900">{{ $user->salesRep->totalOrders }}</p>
                            </a>

                            <a href="{{ route('admin.allRequests') }}" class="rounded-xl border border-gray-100 p-4 text-center hover:shadow-md hover:-translate-y-0.5 transition-all">
                                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-amber-50 text-amber-600 mb-2"><i class="bi bi-hourglass-split"></i></span>
                                <p class="text-xs font-medium text-gray-500">الطلبات المعلقة</p>
                                <p class="text-xl font-semibold text-amber-600">{{ $user->salesRep->totalPendedRequests }}</p>
                            </a>

                            <a href="{{ route('sales-reps.clients.index', $user->salesRep->id) }}?filter=late" class="rounded-xl border border-gray-100 p-4 text-center hover:shadow-md hover:-translate-y-0.5 transition-all">
                                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-sky-50 text-sky-600 mb-2"><i class="bi bi-star-fill"></i></span>
                                <p class="text-xs font-medium text-gray-500">العملاء المهتمين</p>
                                <p class="text-xl font-semibold text-gray-900">{{ $user->salesRep->interestedClients->count() }}</p>
                            </a>

                            <a href="{{ route('salesrep.agreements.index', $user->salesRep->id) }}" class="rounded-xl border border-gray-100 p-4 text-center hover:shadow-md hover:-translate-y-0.5 transition-all">
                                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-indigo-50 text-indigo-600 mb-2"><i class="bi bi-file-earmark-text-fill"></i></span>
                                <p class="text-xs font-medium text-gray-500">عدد الاتفاقيات</p>
                                <p class="text-xl font-semibold text-gray-900">{{ $user->salesRep->agreements->count() }}</p>
                            </a>

                            <div class="rounded-xl border border-gray-100 p-4 text-center">
                                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 mb-2"><i class="bi bi-cash-coin"></i></span>
                                <p class="text-xs font-medium text-gray-500">العمولات المستحقة</p>
                                <p class="text-xl font-semibold text-gray-900">{{ number_format($user->salesRep->currentMonthAchievedCommission()) }} ر.س</p>
                            </div>

                            @php
                                $rate = $user->salesRep->totalOrders > 0
                                    ? round(($user->salesRep->totalOrders - $user->salesRep->totalPendedRequests) / $user->salesRep->totalOrders * 100)
                                    : 0;
                            @endphp
                            <div class="rounded-xl border border-gray-100 p-4 text-center">
                                <span class="mx-auto flex h-11 w-11 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 mb-2"><i class="bi bi-graph-up-arrow"></i></span>
                                <p class="text-xs font-medium text-gray-500">معدل القبول</p>
                                <p class="text-xl font-semibold text-gray-900">{{ $rate }}%</p>
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
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    <a href="{{ route('allClients') }}" class="block">
                        <x-stat-card label="إجمالي العملاء" :value="$clientsCount" accent="sky"
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a4 4 0 00-3-3.87M2 20h5v-2a4 4 0 013-3.87M12 4a4 4 0 110 8a4 4 0 010-8z"/></svg>' />
                    </a>

                    <a href="{{ route('allAgreements') }}" class="block">
                        <x-stat-card label="إجمالي الاتفاقيات" :value="$agreementsCount" accent="emerald"
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2l4-4M12 22a10 10 0 100-20 10 10 0 000 20z"/></svg>' />
                    </a>

                    <a href="{{ route('admin.allRequests') }}" class="block">
                        <x-stat-card label="طلبات العملاء" :value="$clientRequestsCount" accent="amber"
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7h18M3 12h18M3 17h18"/></svg>' />
                    </a>

                    <a href="{{ url('/sales-reps') }}" class="block">
                        <x-stat-card label="عدد مندوبي المبيعات" :value="$salesRepsCount" accent="indigo"
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14v7m-6 0h12"/></svg>' />
                    </a>

                    <x-stat-card label="أهداف الشهر المتحققة" :value="$achievedTargetsCount" accent="indigo"
                        icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0a9 9 0 0118 0z"/></svg>' />

                    <a href="/admin/allRequests" class="block">
                        <x-stat-card label="الطلبات المعلقة" :value="$pendingRequestsCount" accent="rose"
                            icon='<svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 5.636l-12.728 12.728M5.636 5.636l12.728 12.728"/></svg>' />
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function togglePasswordVisibilityProfile(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);

        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    @if(Auth::user()->role === 'admin' && isset($salesRep))
    document.addEventListener('DOMContentLoaded', function() {
        const passwordForm = document.getElementById('salesRepPasswordForm');

        if (passwordForm) {
            passwordForm.addEventListener('submit', async function(e) {
                e.preventDefault();

                const newPassword = document.getElementById('salesrepNewPassword').value;
                const confirmPassword = document.getElementById('salesrepConfirmPassword').value;

                if (newPassword !== confirmPassword) {
                    alert('كلمة المرور وتأكيدها غير متطابقين');
                    return;
                }

                const submitBtn = passwordForm.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.innerText = 'جاري الحفظ...';

                try {
                    const response = await fetch(passwordForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            _method: 'PUT',
                            salesrepPassword: newPassword,
                            salesrepPassword_confirmation: confirmPassword
                        })
                    });

                    const data = await response.json();

                    if (!response.ok) {
                        if (data.errors) {
                            let errorMessages = [];
                            for (const field in data.errors) {
                                errorMessages.push(...data.errors[field]);
                            }
                            alert(errorMessages.join('\n'));
                        } else {
                            throw new Error(data.message || 'حدث خطأ أثناء تحديث كلمة المرور');
                        }
                        return;
                    }

                    alert(data.message || 'تم تحديث كلمة المرور بنجاح');
                    passwordForm.reset();
                    window.location.reload();
                } catch (error) {
                    console.error('Error:', error);
                    alert(error.message || 'حدث خطأ غير متوقع');
                } finally {
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'حفظ كلمة المرور';
                }
            });
        }
    });
    @endif
</script>

@if($user->role === 'salesRep' && $user->salesRep && Auth::user()->role === 'admin')
    <!-- Assign Manager Modal -->
    <div class="modal fade" id="assignManagerModal" tabindex="-1" aria-labelledby="assignManagerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-2xl border-0 shadow-xl">
                <div class="modal-header border-b border-gray-100">
                    <h5 class="modal-title font-semibold" id="assignManagerModalLabel">تعيين مدير</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('manager.assign', $user->salesRep) }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="manager_id" class="block text-sm font-medium text-gray-700 mb-1.5">اختر المدير</label>
                            <select name="manager_id" id="manager_id" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                <option value="">اختر مدير...</option>
                                @foreach(\App\Models\SalesRep::where('id', '!=', $user->salesRep->id)->with('user')->get() as $potentialManager)
                                    @if($potentialManager->canBeAssignedAsManagerTo($user->salesRep))
                                        <option value="{{ $potentialManager->id }}" {{ $user->salesRep->manager_id == $potentialManager->id ? 'selected' : '' }}>
                                            {{ $potentialManager->name }} ({{ $potentialManager->user->email }})
                                        </option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-t border-gray-100">
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700">تعيين المدير</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Remove Manager Modal -->
    <div class="modal fade" id="removeManagerModal" tabindex="-1" aria-labelledby="removeManagerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content rounded-2xl border-0 shadow-xl">
                <div class="modal-header border-b border-gray-100">
                    <h5 class="modal-title font-semibold" id="removeManagerModalLabel">إزالة المدير</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('manager.remove', $user->salesRep) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <div class="modal-body">
                        <p class="text-sm text-gray-600">هل أنت متأكد من إزالة تعيين المدير لـ {{ $user->salesRep->name }}؟</p>
                    </div>
                    <div class="modal-footer border-t border-gray-100">
                        <button type="button" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="px-4 py-2 bg-amber-500 text-white rounded-lg text-sm font-medium hover:bg-amber-600">إزالة المدير</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif

<script>
    document.getElementById('profilePhotoInput').addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];

            if (!validTypes.includes(file.type)) {
                alert('الرجاء اختيار صورة بصيغة JPEG أو PNG أو GIF');
                return;
            }

            if (file.size > 2048 * 1024) {
                alert('حجم الملف يجب أن لا يتجاوز 2MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profileImage').src = e.target.result;
            }
            reader.readAsDataURL(file);

            document.getElementById('avatarUploadForm').submit();

            const editBtn = document.querySelector('.avatar-edit-btn');
            editBtn.innerHTML = '<i class="bi bi-arrow-clockwise animate-spin"></i>';
            editBtn.disabled = true;
        }
    });
</script>
@endsection
