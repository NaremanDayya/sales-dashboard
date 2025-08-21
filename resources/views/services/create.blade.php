@extends('layouts.master')

@section('title', 'إضافة خدمة')

@section('content')
<div class="container">
<div style="margin-bottom: 20px;">
    <h1 style="text-align: center; font-size: 24px; font-weight: 800;">
        إضافة خدمة جديدة
    </h1>
</div>

<form action="{{ route('services.store') }}" method="POST">
    @csrf
    @include('services._form', [
        'button_label' => __('إضافة خدمة')
    ])
</form>
</div>
@endsection

