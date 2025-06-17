@extends('layouts.master')
@section('title', 'Edit Sales Representative')
@section('content')
<div class="container">
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Edit Sales Representative: {{ $salesRep->name }}</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('sales-reps.update', $salesRep->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('salesRep._form', ['button_label' => __('Update')])

                {{-- Permissions Section --}}
                @can('assign_permissions')
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-light">
                                <h5 class="mb-0">
                                    <i class="bi bi-shield-lock me-2"></i> Permissions
                                </h5>
                            </div>
                            <div class="card-body">
                                @if($allPermissions->isEmpty())
                                    <div class="alert alert-info mb-0">
                                        No permissions available for assignment
                                    </div>
                                @else
                                    <div class="row">
                                        @foreach($allPermissions->chunk(ceil($allPermissions->count()/2)) as $chunk)
                                        <div class="col-md-6">
                                            @foreach($chunk as $permission)
                                            <div class="form-check mb-3">
                                                <input class="form-check-input"
                                                       type="checkbox"
                                                       name="permissions[]"
                                                       value="{{ $permission->id }}"
                                                       id="perm-{{ $permission->id }}"
                                                       {{ $salesRep->user->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="perm-{{ $permission->id }}">
                                                    {{ $permission->description ?? ucwords(str_replace('_', ' ', $permission->name)) }}
                                                </label>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endcan

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-1"></i> Save Changes
                    </button>
                    <a href="{{ route('sales-reps.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle me-1"></i> Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
