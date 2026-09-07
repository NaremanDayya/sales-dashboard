@extends('layouts.master')

@section('title', 'إضافة خدمة')

@section('content')
<x-page-header title="إضافة خدمة جديدة" subtitle="حدد اسم الخدمة، الهدف المطلوب تحقيقه، ونسبة العمولة" />

<form action="{{ route('services.store') }}" method="POST">
    @csrf
    @include('services._form', [
        'button_label' => __('إضافة خدمة')
    ])
</form>
@endsection
