@extends('layouts.master')
@section('title', 'Edit Client')
@section('content')
<x-page-header :title="'تعديل بيانات ' . $client->name" subtitle="حدّث بيانات العميل أدناه" />

<form action="{{ route('sales-reps.clients.update', ['sales_rep' => $salesRep->id,'client'=> $client->id]) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('clients._form', [
        'button_label' => __('Edit')
    ])
</form>

<!-- Permissions Section (Admin Only) -->
@can('assign_permissions')
    <div class="max-w-4xl mx-auto mt-6">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h4 class="text-sm font-semibold text-gray-900">إدارة الصلاحيات</h4>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('sales-reps.update', $salesRep) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-4">
                        <h5 class="text-sm font-medium text-gray-700 mb-3">الصلاحيات المتاحة:</h5>
                        <div class="space-y-2">
                            @foreach($allPermissions as $permission)
                                <div class="flex items-center gap-2">
                                    <input type="checkbox"
                                           name="permissions[]" value="{{ $permission->id }}"
                                           id="perm-{{ $permission->id }}"
                                           {{ in_array($permission->id, $currentPermissions) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    <label for="perm-{{ $permission->id }}" class="text-sm text-gray-700">
                                        {{ $permission->description ?? $permission->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                        تحديث الصلاحيات
                    </button>
                </form>
            </div>
        </div>
    </div>
@endcan
@endsection
