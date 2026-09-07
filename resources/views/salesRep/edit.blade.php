@extends('layouts.master')
@section('title', 'Edit Sales Representative')
@section('content')
<x-page-header :title="'تعديل بيانات ' . $salesRep->name" subtitle="حدّث بيانات سفير العلامة التجارية" />

<form action="{{ route('sales-reps.update', $salesRep->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    @include('salesRep._form', ['button_label' => __('تعديل')])
</form>
@endsection
