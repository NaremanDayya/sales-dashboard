@extends('layouts.master')

@section('title', 'Add Service')

@section('content')
<div class="container">
    <h1>Add Service</h1>
    <form action="{{ route('services.store') }}" method="POST">
        @csrf
        @include('services._form', [
            'button_label' => __('إضافة خدمة')
        ])
    </form>
</div>
@endsection
