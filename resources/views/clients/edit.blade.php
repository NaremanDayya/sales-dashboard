
@extends('layouts.master')
@section('title', 'Edit Client')
@section('content')
<div class="container">
    <h1>Edit Client {{ $client->name }}</h1>

        <form action="{{ route('sales-reps.clients.update', ['sales_rep' => $salesRep->id,'client'=> $client->id]) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('clients._form', [
            'button_label' => __('Edit')
        ])
    </form>

        <!-- Permissions Section (Admin Only) -->
        @can('assign_permissions')
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Manage Permissions</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('sales-reps.update', $salesRep) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <h5>Available Permissions:</h5>
                            @foreach($allPermissions as $permission)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="permissions[]" value="{{ $permission->id }}"
                                       id="perm-{{ $permission->id }}"
                                       {{ in_array($permission->id, $currentPermissions) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm-{{ $permission->id }}">
                                    {{ $permission->description ?? $permission->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>

                        <button type="submit" class="btn btn-primary">Update Permissions</button>
                    </form>
                </div>
            </div>
        </div>
        @endcan
</div>
@endsection

