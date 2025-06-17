@extends('layouts.master')
@section('title', 'Add Sales Representative')
@section('content')
<div class="container">
    <h1> Add Sales Representative</h1>
    <form action="{{ route('sales-reps.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('salesRep._form', [
        'button_label' => __('إضافة مندوب '),
        'allPermissions' => $allPermissions ?? collect(),
        'selectedPermission' => [],

        ])
    </form>
</div>
@endsection
