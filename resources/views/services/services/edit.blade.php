@extends('layouts.master')

@section('title', 'Edit Service')

@section('content')
<div class="container">
<div style="margin-bottom: 20px;">
    <h1 style="text-align: center; font-size: 24px; font-weight: 800;">
تعديل خدمة {{ $service->name}} 
    </h1>
</div>

    <form action="{{ route('services.update', $service->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('services._form', [
            'button_label' => __('تعديل الخدمة')
        ])
    </form>
</div>
@endsection
