@extends('layouts.master')

@section('title', 'Add Target')

@section('content')
<div class="container">
    <h1>Add Target</h1>
    <form action="{{ route('targets.store') }}" method="POST">
        @csrf
        @include('targets._form', [
            'button_label' => __('Create')
        ])
    </form>
</div>
@endsection
