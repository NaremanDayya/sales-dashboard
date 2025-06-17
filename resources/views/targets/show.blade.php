@extends('layouts.master')
@section('title', 'View Target')
@section('content')
<div class="container py-5">
    <div class="text-center mb-4">
        <h1 class="fw-bold text-primary animate__animated animate__fadeInDown">
            🎯 Target Details
        </h1>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 animate__animated animate__fadeInUp">
                <div class="card-body">
                    <h5 class="card-title text-success"><i class="fas fa-user"></i> Sales Rep</h5>
                    <p class="card-text">{{ $target->user->name }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 animate__animated animate__fadeInUp animate__delay-1s">
                <div class="card-body">
                    <h5 class="card-title text-info"><i class="fas fa-cogs"></i> Service</h5>
                    <p class="card-text">{{ $target->service->name }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 animate__animated animate__fadeInUp animate__delay-2s">
                <div class="card-body">
                    <h5 class="card-title text-warning"><i class="fas fa-calendar-alt"></i> Month</h5>
                    <p class="card-text">{{ $target->month }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 animate__animated animate__fadeInUp animate__delay-3s">
                <div class="card-body">
                    <h5 class="card-title text-primary"><i class="fas fa-calendar"></i> Year</h5>
                    <p class="card-text">{{ $target->year }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 animate__animated animate__fadeInUp animate__delay-4s">
                <div class="card-body">
                    <h5 class="card-title text-danger"><i class="fas fa-bullseye"></i> Target Amount</h5>
                    <p class="card-text">{{ $target->target_amount }}</p>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm border-0 animate__animated animate__fadeInUp animate__delay-5s">
                <div class="card-body">
                    <h5 class="card-title text-secondary"><i class="fas fa-chart-line"></i> Carried Over</h5>
                    <p class="card-text">{{ $target->carried_over_amount }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-5 d-flex justify-content-center gap-3 animate__animated animate__fadeInUp animate__delay-6s">
        <a href="{{ route('targets.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
</div>
@endsection
