@extends('layouts.master')
@section('title', '404')
@section('content')
        <div class="text-center max-w-3xl mx-auto py-12">
            <div class="floating mb-8">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-40 w-40 mx-auto text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>

            <h1 class="text-5xl font-bold text-gray-900 mb-4">404</h1>
            <h2 class="text-3xl font-semibold text-gray-800 mb-6">الصفحة غير موجودة</h2>

            <p class="text-lg text-gray-600 mb-8 max-w-2xl mx-auto">
                الصفحة التي تحاول الوصول إليها غير موجودة
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('dashboard') }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors duration-200">
                    لوحة التحكم
                </a>
                <a href="{{ url()->previous() }}" class="px-6 py-3 border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg shadow-sm transition-colors duration-200">
                    الرجوع للخلف
                </a>
            </div>


        </div>

@endsection

