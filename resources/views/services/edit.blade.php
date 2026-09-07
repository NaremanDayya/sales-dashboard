@extends('layouts.master')

@section('title', 'تعديل خدمة')

@section('content')
<x-page-header :title="'تعديل خدمة ' . $service->name" subtitle="حدّث بيانات الخدمة أدناه" />

<form action="{{ route('services.update', $service->id) }}" method="POST">
    @csrf
    @method('PUT')
    @include('services._form', [
        'button_label' => __('تعديل الخدمة')
    ])
</form>
@endsection
