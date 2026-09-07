@extends('layouts.master')
@section('title', 'Add Request')
@section('content')
<x-page-header title="تقديم طلب تعديل" :subtitle="'للعميل ' . $client->company_name" />

<div class="max-w-2xl bg-white border border-gray-200 rounded-xl shadow-sm p-6">
    <form action="{{ route('client-request.store')}}" method="POST" enctype="multipart/form-data" class="space-y-5">
        @csrf
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">نوع الطلب</label>
            <select name="request_type" class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                <option value="" disabled selected>اختر نوع الطلب</option>
                @foreach(\App\Models\ClientEditRequest::REQUEST_TYPES as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <input type="hidden" name="client_id" id="" value="{{ $client->id }}">

        <div>
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1.5">وصف الطلب</label>
            <x-form.textarea name="description" placeholder="اشرح تفاصيل الطلب" rows="4"></x-form.textarea>
            @error('description')
                <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end pt-2">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors">
                إرسال الطلب
            </button>
        </div>
    </form>
</div>
@endsection
