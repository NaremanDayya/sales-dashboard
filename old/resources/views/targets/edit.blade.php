@extends('layouts.master')

@section('title', 'Edit Target')

@section('content')
<div class="container">
    <h1>Edit Target for Sales Representative: {{ $target->salesRep->name }} (Service: {{ $target->service->name }})</h1>

    <form action="{{ route('targets.update', $target->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('targets._form', [
            'button_label' => __('Edit')
        ])
    </form>
</div>
@endsection
