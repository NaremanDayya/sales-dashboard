@extends('layouts.master')
@section('title', 'Edit Sales Representative')
@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">تعديل بيانات العميل  {{ $salesRep->name }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('sales-reps.update', $salesRep->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('salesRep._form', ['button_label' => __('تعديل')])


            </form>
        </div>
    </div>
</div>
@endsection
