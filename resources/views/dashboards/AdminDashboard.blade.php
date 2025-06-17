@extends('layouts.master')

@section('content')
<div class="pagetitle">
    <h1>Admin Dashboard</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<section class="section dashboard">
    <div class="row">
        <!-- Top Summary Cards -->
        <div class="col-lg-12">
            <div class="row">
                <!-- Sales Reps Card -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card info-card sales-card">
                        <div class="card-body">
                            <h5 class="card-title">Sales Team</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-people"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $data['allSalesReps'] }}</h6>
                                    <span class="text-success small pt-1 fw-bold">{{ $data['repsGrowth'] }}%</span>
                                    <span class="text-muted small pt-2 ps-1">growth</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="progress mt-2" style="height: 8px;">
                                <div class="progress-bar bg-success"
                                    style="width: {{ min($data['repsGrowth'] + 50, 100) }}%"></div>
                            </div>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">{{ $data['activeRepsCount'] }} active</small>
                                <small class="text-muted">{{ $data['newRepsThisMonth'] }} new</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clients Card -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card info-card revenue-card">
                        <div class="card-body">
                            <h5 class="card-title">Clients</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $data['totalClients'] }}</h6>
                                    <span class="text-success small pt-1 fw-bold">{{ $data['clientGrowth'] ?? 8
                                        }}%</span>
                                    <span class="text-muted small pt-2 ps-1">increase</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-success">{{ $data['inerestedClients'] }} active</span>
                                <span class="badge bg-warning">{{ $data['notInerestedClients'] }} inactive</span>
                            </div>
                            <div class="mt-2 text-center">
                                <a href="{{ route('allClients') }}" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Agreements Card -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card info-card customers-card">
                        <div class="card-body">
                            <h5 class="card-title">Agreements</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-file-text"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $data['totalAgreements'] }}</h6>
                                    <span class="text-danger small pt-1 fw-bold">{{ $data['agreementChange'] ?? -2
                                        }}%</span>
                                    <span class="text-muted small pt-2 ps-1">this month</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-success">{{ $data['activeAgreements'] }} active</span>
                                <span class="badge bg-warning">{{ $data['pendingAgreements'] }} pending</span>
                            </div>
                            <div class="mt-2 text-center">
                                <span class="badge bg-danger">{{ $data['expiringSoon'] }} expiring</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Targets Card -->
                <div class="col-xxl-3 col-md-6">
                    <div class="card info-card targets-card">
                        <div class="card-body">
                            <h5 class="card-title">Targets</h5>
                            <div class="d-flex align-items-center">
                                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="bi bi-bullseye"></i>
                                </div>
                                <div class="ps-3">
                                    <h6>{{ $data['onTargetCount'] }}/{{ $data['totalTargets'] ?? $data['allSalesReps']
                                        }}</h6>
                                    <span class="text-success small pt-1 fw-bold">{{ $data['targetAchievement'] ?? 75
                                        }}%</span>
                                    <span class="text-muted small pt-2 ps-1">achieved</span>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="d-flex justify-content-between">
                                <span class="badge bg-success">{{ $data['onTargetCount'] }} on target</span>
                                <span class="badge bg-warning">{{ $data['nearTargetCount'] }} near</span>
                                <span class="badge bg-danger">{{ $data['belowTargetCount'] }} below</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-lg-8">
            <!-- Performance Chart -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Monthly Performance <span>| Current Year</span></h5>
                    <div id="performanceChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
        <!-- Right Sidebar -->
        <div class="col-lg-4">
            <!-- Recent Activity -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Recent Activity <span>| Today</span></h5>
                    <div class="activity">
                        @foreach($data['recentActivities'] ?? [] as $activity)
                        <div class="activity-item d-flex">
                            <div class="activite-label">{{ $activity['time'] ?? '32 min' }}</div>
                            <i
                                class="bi bi-circle-fill activity-badge {{ $activity['type'] ?? 'text-success' }} align-self-start"></i>
                            <div class="activity-content">
                                {{ $activity['message'] ?? 'New client added' }}
                                <br>
                                <small class="text-muted">{{ $activity['user'] ?? 'By John Doe' }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Stats</h5>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <h6>Avg. Deal Size</h6>
                            <p>${{ number_format($data['avgDealSize'] ?? 2450, 2) }}</p>
                        </div>
                        <div class="stat-item">
                            <h6>Conversion Rate</h6>
                            <p>{{ $data['conversionRate'] ?? 22 }}%</p>
                        </div>
                        <div class="stat-item">
                            <h6>Response Time</h6>
                            <p>{{ $data['avgResponseTime'] ?? 2.5 }} hrs</p>
                        </div>
                        <div class="stat-item">
                            <h6>Satisfaction</h6>
                            <p>{{ $data['satisfactionRate'] ?? 92 }}%</p>
                        </div>
                    </div>
                </div>
            </div>

           
        </div>
    </div>
</section>

@push('styles')
<style>
    .info-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 10px;
        margin-bottom: 20px;
        overflow: hidden;
    }

    .info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .info-card .card-icon {
        font-size: 1.5rem;
        color: #fff;
        width: 56px;
        height: 56px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sales-card {
        background: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
        color: white;
    }

    .revenue-card {
        background: linear-gradient(135deg, #4cc9f0 0%, #4895ef 100%);
        color: white;
    }

    .customers-card {
        background: linear-gradient(135deg, #7209b7 0%, #560bad 100%);
        color: white;
    }

    .targets-card {
        background: linear-gradient(135deg, #f72585 0%, #b5179e 100%);
        color: white;
    }

    .info-card .card-footer {
        background: rgba(255, 255, 255, 0.1);
        border-top: 1px solid rgba(255, 255, 255, 0.2);
        padding: 15px;
    }

    .activity .activity-item {
        position: relative;
        padding-bottom: 20px;
        padding-left: 30px;
        border-left: 2px solid #f0f0f0;
    }

    .activity .activity-item:last-child {
        border-left: 2px solid transparent;
    }

    .activity .activity-item .activity-badge {
        position: absolute;
        left: -7px;
        top: 0;
    }

    .activity .activite-label {
        color: #888;
        width: 70px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }

    .stat-item {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        text-align: center;
    }

    .stat-item h6 {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 5px;
    }

    .stat-item p {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 0;
    }

    .task-list {
        list-style: none;
        padding-left: 0;
    }

    .task-list li {
        padding: 8px 0;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Performance Chart
        const performanceChart = echarts.init(document.querySelector("#performanceChart"));
        performanceChart.setOption({
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    type: 'shadow'
                }
            },
            legend: {
                data: ['Clients', 'Agreements', 'Targets']
            },
            grid: {
                left: '3%',
                right: '4%',
                bottom: '3%',
                containLabel: true
            },
            xAxis: {
                type: 'category',
                data: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
            },
            yAxis: {
                type: 'value'
            },
            series: [
                {
                    name: 'Clients',
                    type: 'bar',
                    data: [120, 132, 101, 134, 90, 230, 210, 182, 191, 234, 290, 330],
                    itemStyle: {
                        color: '#4cc9f0'
                    }
                },
                {
                    name: 'Agreements',
                    type: 'bar',
                    data: [80, 92, 71, 94, 60, 130, 110, 82, 91, 114, 150, 180],
                    itemStyle: {
                        color: '#7209b7'
                    }
                },
                {
                    name: 'Targets',
                    type: 'line',
                    data: [100, 120, 110, 120, 100, 200, 180, 160, 170, 200, 250, 300],
                    itemStyle: {
                        color: '#f72585'
                    }
                }
            ]
        });

        window.addEventListener('resize', function() {
            performanceChart.resize();
        });
    });
</script>
@endpush
@endsection
