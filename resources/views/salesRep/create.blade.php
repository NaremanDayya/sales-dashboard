@extends('layouts.master')
@section('title', 'Add Sales Representative')
@section('content')
<x-page-header title="أضف مندوب جديد" subtitle="أنشئ حساب سفير علامة تجارية جديد" />

<form action="{{ route('sales-reps.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
@include('salesRep._form', [
        'button_label' => __('إضافة مندوب '),
        'allPermissions' => $allPermissions ?? collect(),
        'selectedPermission' => [],
])
</form>
@endsection
