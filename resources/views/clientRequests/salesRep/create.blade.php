@extends('layouts.master')
@section('title', 'Add Request')
@section('content')
<div class="container">
    <h1> Submit Request for {{ $client->company_name }}</h1>
    <form action="{{ route('client-request.store')}}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="col-md-4">
            <label class="block font-medium">Request Type</label>
            <select name="request_type" class="w-full border rounded px-3 py-2" required>
                <option value="" disabled selected>Choose request type</option>
                @foreach(\App\Models\ClientEditRequest::REQUEST_TYPES as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

            <input type="hidden" name="client_id" id="" value="{{ $client->id }}">

        <x-form.floating-control name="description">
            <x-slot:label>
                <label for="description">Request Description</label>
            </x-slot:label>
            <x-form.textarea name="description" placeholder="Describe the Request" rows="3"></x-form.textarea>
        </x-form.floating-control>

        <button type="submit" class="btn ms-2" style="background-color: #198754; color: white;">
            Send Request
        </button>
    </form>
    @endsection
