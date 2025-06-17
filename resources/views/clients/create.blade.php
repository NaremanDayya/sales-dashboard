@extends('layouts.master')
@section('title', 'Add Client')
@section('content')
<div class="container">
    <form action="{{ route('sales-reps.clients.store', Auth::user()->salesRep->id) }}" method="POST" enctype="multipart/form-data"
        x-data="clientWizard()" x-init="init()">
        @csrf
        <div class="mb-8 bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h3 class="text-lg font-medium text-gray-800 mb-4">شعار الشركة</h3>
            <div class="flex items-center space-x-6">
                <div x-show="previewLogo" class="shrink-0">
                    <img :src="previewLogo" class="h-16 w-16 object-cover rounded-full border border-gray-200">
                </div>
                <div class="w-full">
                    <label class="block">
                        <span class="sr-only">إختر شعار الشركة</span>
                        <input type="file" name="company_logo" @change="handleLogoUpload" class="block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100" accept="image/*">
                    </label>
                    <p class="mt-1 text-xs text-gray-500">PNG, JPG or GIF (MAX. 2MB)</p>
                </div>
            </div>
        </div>

        @include('clients._form', [
        'button_label' => __('Create')
        ])
    </form>
</div>
@endsection
