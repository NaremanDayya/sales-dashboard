@extends('layouts.master')
@section('title', 'تفاصيل العميل')
@section('content')
    @php use Illuminate\Support\Str; @endphp

    <section class="client-profile">
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column (Profile Card) -->
            <div class="w-full lg:w-1/3">
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <!-- Client Header -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-6 text-center">
                        @php
                            $logo = $client->company_logo;
                            $isUrl = Str::startsWith($logo, ['http://', 'https://']);
                            $logoUrl = $isUrl ? $logo : asset('storage/' . $logo);
                        @endphp

                        <div class="flex justify-center">
                            <div class="relative">
                                <img src="{{ $logoUrl }}" onerror="this.src='{{ asset('images/default-company.png') }}'"
                                     alt="Logo"
                                     class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-md">
                            </div>
                        </div>

                        <h1 class="text-2xl font-bold text-gray-800 mt-4">{{ $client->company_name }}</h1>
                        <p class="text-gray-600">{{ $client->contact_person }}</p>

                        @if(Auth::user()->role == 'salesRep')
                            <a href="{{ route('salesrep.agreements.create', ['salesrep' => $client->salesRep->id, 'client_id' => $client->id]) }}"
                               class="inline-flex items-center mt-4 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-all shadow hover:shadow-md">
                                <i class="fas fa-file-contract mr-2"></i> إنشاء اتفاقية
                            </a>

                        @endif

                        <a href="{{ route('client.message',$client->id) }}"
                           class="inline-flex items-center mt-4 ml-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-all shadow hover:shadow-md">
                            <i class="fas fa-comments mr-2"></i> مراسلة
                        </a></div>

                    <!-- Quick Stats -->
                    <div class="p-6 border-b">
                        <div class="grid grid-cols-3 gap-4 text-center">
                            <div>
                                <p class="text-sm text-gray-500">الاتفاقيات</p>
                                <p class="text-lg font-semibold">{{ $client->agreements->count() }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">الطلبات</p>
                                <p class="text-lg font-semibold">{{ $client->allEditRequests()->count() }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">أيام التأخير</p>
                                <p
                                        class="text-lg font-semibold {{ $client->late_days > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $client->late_days }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Requests -->
                    <div class="p-6">
                        <h3 class="flex items-center space-x-2 rtl:space-x-reverse text-lg font-semibold text-gray-700 mb-4">
                            <i class="fas fa-history text-blue-500"></i>
                            <span>طلبات التعديل الأخيرة</span>
                        </h3>
                        <div class="space-y-3 max-h-96 overflow-y-auto pr-2">
                            @forelse($client->clientEditRequests()->take(3)->latest()->get() as $request)
                                <a href="{{ route(
                Auth::user()->role === 'admin' ? 'admin.client-request.edit' : 'admin.client-request.review',
                ['client' => $request->client_id, 'client_request' => $request->id]
            ) }}" class="block border rounded-lg p-4 hover:bg-gray-50 transition cursor-pointer">
                                    <div class="flex justify-between items-start">
                    <span class="font-medium text-gray-800">
                        تعديل بيانات العميل
                    </span>
                                        @php
                                            $statusLabels = [
                                                'pending' => 'قيد المراجعة',
                                                'approved' => 'تمت الموافقة',
                                                'rejected' => 'مرفوض'
                                            ];

                                            $fieldLabels = [
                                                'company_name' => 'اسم الشركة',
                                        'logo' => 'الشعار',
                                        'address' => 'العنوان',
                                        'last_contact_date' => 'تاريخ اخر تواصل',
                                        'contact_person' => 'الشخص المسؤول',
                                        'interest_status' => 'حالة الاهتمام',
                                        'phone' => 'رقم الهاتف',
                                        'contact_position' => 'منصب المسؤول',
                                            ];
                                        @endphp
                                        <span
                                                class="px-2 py-1 rounded-full text-xs font-medium
                            {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                ($request->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                        {{ $statusLabels[$request->status] ?? $request->status }}
                    </span>
                                    </div>

                                    {{-- حقل المعدَّل --}}
                                    <div class="mt-2 text-sm">
                    <span class="inline-block px-2 py-1 bg-blue-100 text-blue-700 rounded text-xs font-medium">
                        {{ $fieldLabels[$request->edited_field] ?? $request->edited_field }}
                    </span>
                                    </div>

                                    {{-- الوصف --}}
                                    <p class="text-sm text-gray-600 mt-2 line-clamp-2">
                                        {{ $request->description }}
                                    </p>

                                    {{-- التاريخ والحالة --}}
                                    <div class="flex justify-between items-center mt-3">
                    <span class="text-xs text-gray-400">
                        {{ $request->created_at->format('Y-m-d H:i') }}
                        <span class="mx-1 text-gray-300">|</span>
                        {{ $request->created_at->diffForHumans() }}
                    </span>
                                        @if($request->status === 'pending')
                                            <span class="text-xs text-yellow-600 animate-pulse">
                            <i class="fas fa-clock mr-1"></i> قيد المراجعة
                        </span>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <div class="text-center py-6 text-gray-400">
                                    <i class="fas fa-inbox text-3xl mb-2"></i>
                                    <p>لا توجد طلبات تعديل</p>
                                </div>
                            @endforelse
                        </div>

                        @if($client->clientEditRequests()->count() > 3)
                            <a href="{{route('myRequests',$client->sales_rep_id)}}"
                               class="mt-4 inline-flex items-center text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-arrow-down mr-1"></i> عرض
                                كل {{ $client->clientEditRequests()->count() }} طلب
                            </a>
                        @endif
                    </div>
                </div>
            </div>
                <!-- Right Column (Details and Forms) -->
                <div class="w-full lg:w-2/3">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                        <!-- Tab Navigation -->
                        <div class="border-b border-gray-200 mb-4">
                            <nav class="flex justify-start space-x-4 rtl:space-x-reverse pt-3 -mb-px mb-4 mr-3">
                                <button class="tab-button active flex items-center space-x-2 rtl:space-x-reverse"
                                        data-tab="overview">
                                    <i class="fas fa-info-circle"></i>
                                    <span>نظرة عامة</span>
                                </button>

                                @php $canEdit = Auth::user()->hasActiveEditPermission($client, $editableField); @endphp
                                @if($editableField !== 'last_contact_date' && $canEdit)
                                    <button class="tab-button flex items-center space-x-2 rtl:space-x-reverse"
                                            data-tab="edit">
                                        <i class="fas fa-edit"></i>
                                        <span>تعديل البيانات</span>
                                    </button>
                                @endif
                            </nav>
                        </div>

                        <!-- Tab Content -->
                        <div class="p-6">
                            <!-- Overview Tab -->
                            <div id="overview-tab" class="tab-content active">

                                <div class="bg-white p-6 rounded-2xl shadow-md space-y-6">
                                    <h2 class="text-xl font-semibold text-gray-800 border-b pb-2">بيانات العميل</h2>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 text-sm text-gray-700">

                                        <!-- Column 1 -->
                                        <div class="space-y-5">
                                            <div>
                                                <div class="text-gray-500 mb-1">اسم الشركة</div>
                                                <div class="font-semibold">{{ $client->company_name }}</div>
                                            </div>

                                            <div>
                                                <div class="text-gray-500 mb-1">اسم المسؤول</div>
                                                <div class="font-semibold">{{ $client->contact_person }}</div>
                                            </div>

                                            <div>
                                                <div class="text-gray-500 mb-1">العنوان</div>
                                                <div class="font-semibold">{{ $client->address }}</div>
                                            </div>

                                            <div>
                                                <div class="text-gray-500 mb-1">رقم الهاتف</div>
                                                <a href="tel:{{ $client->phone }}"
                                                   class="ltr-number flex items-center gap-1"
                                                   dir="ltr">
                                                    <i class="fas fa-phone-alt"></i>
                                                    {{ Str::startsWith($client->phone, '+') ? $client->phone : '+' . $client->phone }}
                                                </a>
                                            </div>
                                        </div>

                                        <!-- Column 2 -->
                                        <div class="space-y-5">
                                            <div>
                                                <div class="text-gray-500 mb-1">واتساب</div>
                                                <a href="{{ $client->whatsapp_link }}" target="_blank"
                                                   class="text-green-600 font-semibold hover:underline flex items-center space-x-2 rtl:space-x-reverse">
                                                    <i class="fab fa-whatsapp text-lg"></i>
                                                    <span>تواصل الآن</span>
                                                </a></div>

                                            <div>
                                                <div class="text-gray-500 mb-1">سفير العلامة التجارية</div>
                                                <div class="flex items-center space-x-2 rtl:space-x-reverse">
                                                    <i class="fas fa-user-tie text-blue-500"></i>
                                                    <a href="{{ route('sales-reps.show', $client->salesRep->id) }}"
                                                       class="text-blue-600 hover:underline font-semibold">
                                                        {{ $client->salesRep->name }}
                                                    </a>
                                                </div>
                                            </div>

                                            <div>
                                                <div class="text-gray-500 mb-1">درجة الاهتمام</div>
                                                <span
                                                        class="px-3 py-1 rounded-full text-xs font-medium
                    {{ $client->interest_status === 'interested' ? 'bg-green-100 text-green-700' :
                       ($client->interest_status === 'not_interested' ? 'bg-yellow-100 text-red-800' : 'bg-gray-100 text-orange-800') }}">
                                            {{ $client->interest_status === 'interested' ? 'مهتم' :
                                            ($client->interest_status === 'not_interested' ? 'غير مهتم' : 'مؤجل') }}
                                        </span>
                                            </div>

                                            <div>
                                                <div class="text-gray-500 mb-1">آخر تواصل</div>
                                                <div class="flex items-center justify-between font-semibold">
                                            <span>
                                                {{ $client->last_contact_date?->format('d-m-Y') ?? 'لم يتم التواصل بعد'
                                                }}
                                            </span>
                                                    @if(Auth::user()->role == 'salesRep')
                                                        <button onclick="openLastContactModal()"
                                                                class="text-sm bg-blue-100 hover:bg-blue-200 text-blue-800 px-3 py-1 rounded-full">
                                                            <i class="fas fa-edit mr-1"></i> تحديث
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Sales Rep Specific Sections -->
                                @if(Auth::user()->role == 'salesRep')
                                    <!-- Edit Request Form -->
                                    <div class="mt-8 bg-blue-50 rounded-xl p-6 border border-blue-100">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                            <i class="fas fa-edit text-blue-500 mr-2"></i> طلب تعديل بيانات العميل
                                        </h3>

                                        <form class="prevent-multi-submit" method="POST"
                                              action="{{ route('client-edit-requests.store', $client) }}">
                                            @csrf
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">الحقل
                                                        المطلوب
                                                        تعديله</label>
                                                    <select name="edited_field" required
                                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                                                        <option value="" disabled selected>اختر الحقل</option>
                                                        @foreach($columns as $key => $label)
                                                            <option value="{{ $key }}">{{ $label }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">سبب
                                                        التعديل</label>
                                                    <textarea name="update_message" required rows="3"
                                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500"
                                                              placeholder="أضف رسالة توضح سبب التعديل..."></textarea>
                                                </div>

                                                <button type="submit" id="submitBtn"
                                                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-lg transition">
                                                    إرسال طلب التعديل
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Client Request Form -->
                                    <div class="mt-6 bg-purple-50 rounded-xl p-6 border border-purple-100">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-4">
                                            <i class="fas fa-comment-dots text-purple-500 mr-2"></i> تسجيل طلب العميل
                                        </h3>

                                        <form class="prevent-multi-submit" method="POST"
                                              action="{{ route('client-requests.store', $client) }}">
                                            @csrf
                                            <div class="space-y-4">
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">تفاصيل
                                                        الطلب</label>
                                                    <textarea name="request_message" required rows="3"
                                                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500"
                                                              placeholder="سجل طلب العميل هنا..."></textarea>
                                                </div>

                                                <button type="submit"
                                                        class="w-full bg-purple-600 hover:bg-purple-700 text-white py-2 px-4 rounded-lg transition">
                                                    تسجيل الطلب
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <!-- Edit Tab -->
                            @if($canEdit)
                                <div id="edit-tab" class="tab-content hidden">
                                    <h2 class="text-xl font-bold text-gray-800 mb-6">تعديل بيانات العميل</h2>

                                    <form action="{{ route('clients.update', $client->id) }}" method="POST"
                                          enctype="multipart/form-data" class="prevent-multi-submit">
                                        @csrf
                                        @method('PUT')

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">اسم
                                                    الشركة</label>
                                                <input type="text" name="company_name"
                                                       value="{{ old('company_name', $client->company_name) }}"
                                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg" {{ $editableField
                                        !=='company_name' ? 'disabled' : '' }} required>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">اسم
                                                    المسؤول</label>
                                                <input type="text" name="contact_person"
                                                       value="{{ old('contact_person', $client->contact_person) }}"
                                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg" {{ $editableField
                                        !=='contact_person' ? 'disabled' : '' }} required>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">منصب
                                                    المسؤول</label>
                                                <input type="text" name="contact_position"
                                                       value="{{ old('contact_position', $client->contact_position) }}"
                                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg" {{ $editableField
                                        !=='contact_position' ? 'disabled' : '' }}>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">العنوان</label>
                                                <input type="text" name="address"
                                                       value="{{ old('address', $client->address) }}"
                                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg" {{ $editableField
                                        !=='address' ? 'disabled' : '' }}>
                                            </div>

                                            <div class="flex gap-3 items-start">
                                                <!-- country code -->
                                                <div class="w-28">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">كود
                                                        الدولة</label>
                                                    <input type="text" name="country_code"
                                                           value="{{ old('country_code', $client->country_code) }}"
                                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                                            {{ $editableField !== 'phone' ? 'disabled' : '' }}>
                                                    <p class="mt-1 text-xs text-gray-500">
                                                        ⚠️ أدخل الكود بدون (+)
                                                    </p>
                                                </div>

                                                <!-- phone -->
                                                <div class="flex-1">
                                                    <label class="block text-sm font-medium text-gray-700 mb-1">رقم
                                                        الهاتف</label>
                                                    <input type="tel" name="phone"
                                                           value="{{ old('phone', $client->phone) }}"
                                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                                            {{ $editableField !== 'phone' ? 'disabled' : '' }}>
                                                    <p class="mt-1 text-xs text-gray-500">
                                                        ⚠️ أدخل الرقم بدون الصفر إذا بدأ بـ 0
                                                    </p>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">رابط
                                                    الواتساب</label>
                                                <input type="url" name="whatsapp_link"
                                                       value="{{ old('whatsapp_link', $client->whatsapp_link) }}"
                                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg" {{ $editableField
                                        !=='whatsapp_link' ? 'disabled' : '' }}>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1"> درجة
                                                    الإهتمام</label>
                                                <select name="interest_status"
                                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                                        {{ $editableField !== 'interest_status' ? 'disabled' : '' }}>
                                                    <option value="interested">مهتم</option>
                                                    <option value="not interested">غير مهتم</option>
                                                    <option value="neutral">محايد</option>
                                                </select>
                                            </div>

                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700 mb-1">شعار
                                                    الشركة</label>
                                                <input type="file" name="company_logo"
                                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg" {{ $editableField
                                        !=='logo' ? 'disabled' : '' }}>
                                            </div>
                                        </div>

                                        <div class="mt-6 flex justify-end space-x-3">
                                            <button type="button" onclick="switchTab('overview')"
                                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                                إلغاء
                                            </button>
                                            <button type="submit"
                                                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                                حفظ التعديلات
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
    </section>

    <!-- Last Contact Modal -->
    <div id="lastContactModal"
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-md mx-4">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">تحديث آخر تواصل</h3>
                    <button onclick="closeLastContactModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <form method="POST" class="prevent-multi-submit"
                      action="{{ route('clients.update-last-contact', $client->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ آخر تواصل</label>
                            <input type="date" name="last_contact_date" required
                                   value="{{ old('last_contact_date', $client->last_contact_date?->format('Y-m-d')) }}"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">تفاصيل التواصل</label>
                            <textarea name="update_message" required rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 rounded-lg"
                                      placeholder="أضف ملاحظات حول التواصل...">{{ old('update_message') }}</textarea>
                        </div>

                        <div class="flex justify-end space-x-3 pt-2">
                            <button type="button" onclick="closeLastContactModal()"
                                    class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                                إلغاء
                            </button>
                            <button type="submit"
                                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition">
                                حفظ التحديث
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <style>
        .detail-item {
            @apply flex justify-between items-start py-2 border-b border-gray-100;
        }

        .ltr-number {
            direction: ltr;
            unicode-bidi: embed;
            display: inline-block;
            color: blue;
        }

        .detail-label {
            @apply text-gray-600 font-medium w-1/3;
        }

        .detail-value {
            @apply text-gray-800 w-2/3;
        }

        .tab-button {
            @apply px-4 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover: text-gray-700 hover:border-gray-300 transition;
        }

        .tab-button.active {
            @apply text-blue-600 border-blue-600;
        }

        .tab-content {
            @apply hidden;
        }

        .tab-content.active {
            @apply block;
        }

    </style>

@endsection

@push('scripts')
    <script>
        // Tab switching functionality
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
                tab.classList.add('hidden');
            });

            // Show selected tab content
            document.getElementById(`${tabName}-tab`).classList.remove('hidden');
            document.getElementById(`${tabName}-tab`).classList.add('active');

            // Update tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            document.querySelector(`.tab-button[data-tab="${tabName}"]`).classList.add('active');
        }

        // Set up tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.addEventListener('click', function () {
                switchTab(this.dataset.tab);
            });
        });

        // Last contact modal functions
        function openLastContactModal() {
            document.getElementById('lastContactModal').classList.remove('hidden');
        }

        function closeLastContactModal() {
            document.getElementById('lastContactModal').classList.add('hidden');
        }

        // Field selection for edit requests
        document.addEventListener('DOMContentLoaded', function () {
            const select = document.querySelector('select[name="edited_field"]');
            const textarea = document.querySelector('textarea[name="update_message"]');

            if (select && textarea) {
                select.addEventListener('change', function () {
                    const selectedLabel = this.options[this.selectedIndex].text;
                    textarea.placeholder = `أضف رسالة حول سبب تغيير ${selectedLabel}...`;
                });
            }
        });

    </script>
    <script>
        document.querySelectorAll(".prevent-multi-submit").forEach(form => {
            form.addEventListener("submit", function (e) {
                let button = form.querySelector("button[type=submit]");
                button.disabled = true;

            });
        });
    </script>
@endpush
