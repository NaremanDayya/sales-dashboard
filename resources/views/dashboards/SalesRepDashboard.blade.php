@extends('layouts.master')
@section('title','SalesRep Dashboard')
@section('content')
<div class="pagetitle">
    <h1>Service Performance Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">
        <!-- Left side columns -->
        <div class="col-lg-8">
            <div class="row">
                <!-- Clients Card -->
                <div class="col-xxl-4 col-md-6">
                    <div class="card info-card clients-card">
                        <div class="card-body">
                            <h5 class="card-title">My Clients <span>| Active</span></h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle bg-primary-light">
                                    <i class="bi bi-people text-primary"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $clientsCount }}</h6>
                                    <span class="text-success small pt-1 fw-bold">{{ $newClientsThisMonth }} new</span>
                                    <span class="text-muted small pt-2 ps-1">this month</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Service Targets Breakdown -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Service Performance</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Service</th>
                                            <th>Target</th>
                                            <th>Completed</th>
                                            <th>Progress</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($serviceTargets as $target)
                                        <tr>
                                            <td>{{ $target->service->name ?? 'N/A' }}</td>
                                            <td>{{ number_format($target->target_amount) }}</td>
                                            <td>{{ number_format($target->achieved_amount) }}</td>

                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Service Performance Chart
        const serviceChart = echarts.init(document.querySelector("#serviceChart"));
        serviceChart.setOption({
            tooltip: {
                trigger: 'item',
                formatter: '{b}: {c} services ({d}%)'
            },
            legend: {
                orient: 'vertical',
                right: 10,
                top: 'center',
                data: {!! json_encode($serviceTargets->pluck('service.name')) !!}
            },
            series: [
                {
                    name: 'Service Distribution',
                    type: 'pie',
                    radius: ['50%', '70%'],
                    avoidLabelOverlap: false,
                    label: {
                        show: false,
                        position: 'center'
                    },
                    emphasis: {
                        label: {
                            show: true,
                            fontSize: '18',
                            fontWeight: 'bold'
                        }
                    },
                    labelLine: {
                        show: false
                    },
                    data: {!! json_encode($serviceTargets->map(function($target) {
                        return [
                            'value' => $target->achieved_amount,
                            'name' => $target->service->name ?? 'N/A'
                        ];
                    })) !!}
                }
            ]
        });

        window.addEventListener('resize', function() {
            serviceChart.resize();
        });
    });
</script>
@endpush
@endsection
