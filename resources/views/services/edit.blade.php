@extends('layouts.master')

@section('title', 'Edit Service')

@section('content')
<div class="container">
    <h1>Edit Service: {{ $service->name }}</h1>

    <form action="{{ route('services.update', $service->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('services._form', [
            'button_label' => __('Edit')
        ])
    </form>
</div>
@endsection
