@extends('layouts.master')
@section('title', 'Add Sales Representative')
@section('content')
<div class="container">
    <div class="mb-6">
        <h1 class="text-center text-2xl font-bold">أضف مندوب جديد</h1>
    </div>

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
