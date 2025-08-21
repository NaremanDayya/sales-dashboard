@extends('layouts.master')
@section('title', 'View Service')
@section('content')
<div class="container">
    <h1>Service: {{ $service->name }}</h1>

    <p><strong>Description:</strong> {{ $service->description }}</p>
    <p><strong>Default Target Amount:</strong> {{ $service->target_percentage }}</p>

    <a href="{{ route('services.edit', $service->id) }}" class="btn btn-primary">Edit</a>
    <a href="{{ route('services.index') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
