<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Representative Profile - {{ $salesRep->name }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Base Styles */
        @page { margin: 0; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #1e293b;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }

        /* Print-specific styles */
        @media print {
            body { background-color: white; }
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            .page-break { page-break-after: always; }
        }

        /* Layout */
        .container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0;
            background-color: white;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            padding: 40px 40px 60px;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: "";
            position: absolute;
            top: -50px;
            right: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .header::after {
            content: "";
            position: absolute;
            bottom: -80px;
            right: -30px;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
        }

        .header-content {
            position: relative;
            z-index: 2;
        }

        .header-title {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
            letter-spacing: -0.5px;
        }

        .header-subtitle {
            font-size: 16px;
            opacity: 0.9;
            margin: 8px 0 0;
            font-weight: 400;
        }

        /* Profile Section */
        .profile-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            margin: -40px 40px 30px;
            padding: 30px;
            display: flex;
            align-items: center;
            position: relative;
            z-index: 10;
        }

        .avatar {
            width: 100px;
            height: 100px;
            border-radius: 12px;
            background: linear-gradient(135deg, #f59e0b 0%, #ef4444 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 42px;
            font-weight: bold;
            margin-right: 25px;
            flex-shrink: 0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .profile-info {
            flex-grow: 1;
        }

        .profile-name {
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 8px;
            color: #1e293b;
            letter-spacing: -0.25px;
        }

        .profile-title {
            font-size: 16px;
            color: #64748b;
            margin: 0 0 15px;
            font-weight: 500;
        }

        .profile-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 0;
        }

        .meta-item {
            display: flex;
            align-items: center;
            font-size: 14px;
            color: #64748b;
            font-weight: 500;
        }

        .meta-item i {
            margin-right: 8px;
            color: #4f46e5;
            width: 20px;
            text-align: center;
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            padding: 0 40px 30px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            border-left: 4px solid;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .stat-card::after {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0) 100%);
        }

        .stat-card.green {
            border-left-color: #10b981;
        }
        .stat-card.green::after {
            background-color: #10b981;
        }

        .stat-card.blue {
            border-left-color: #3b82f6;
        }
        .stat-card.blue::after {
            background-color: #3b82f6;
        }

        .stat-card.purple {
            border-left-color: #8b5cf6;
        }
        .stat-card.purple::after {
            background-color: #8b5cf6;
        }

        .stat-card.orange {
            border-left-color: #f59e0b;
        }
        .stat-card.orange::after {
            background-color: #f59e0b;
        }

        .stat-card.red {
            border-left-color: #ef4444;
        }
        .stat-card.red::after {
            background-color: #ef4444;
        }

        .stat-card.indigo {
            border-left-color: #6366f1;
        }
        .stat-card.indigo::after {
            background-color: #6366f1;
        }

        .stat-title {
            font-size: 14px;
            color: #64748b;
            margin: 0 0 10px;
            display: flex;
            align-items: center;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-title i {
            margin-right: 10px;
            font-size: 16px;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            margin: 0;
            color: #1e293b;
            letter-spacing: -1px;
        }

        .stat-change {
            font-size: 13px;
            margin-top: 8px;
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .stat-change.positive {
            color: #10b981;
        }

        .stat-change.negative {
            color: #ef4444;
        }

        /* Performance Section */
        .section {
            padding: 0 40px 40px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 700;
            margin: 0 0 20px;
            color: #1e293b;
            display: flex;
            align-items: center;
            letter-spacing: -0.25px;
        }

        .section-title i {
            margin-right: 12px;
            color: #4f46e5;
            font-size: 20px;
        }

        .chart-container {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            margin-bottom: 30px;
        }

        .chart-title {
            font-size: 15px;
            margin: 0 0 20px;
            color: #64748b;
            font-weight: 600;
            display: flex;
            align-items: center;
        }

        .chart-title i {
            margin-right: 10px;
            color: #64748b;
        }

        .chart-placeholder {
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8fafc;
            border-radius: 8px;
            color: #94a3b8;
            font-weight: 500;
        }

        .grid-2col {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        /* Activity Section */
        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .activity-item {
            padding: 20px 0;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: flex-start;
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 8px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 16px;
            margin-right: 15px;
            flex-shrink: 0;
        }

        .activity-content {
            flex-grow: 1;
        }

        .activity-title {
            font-weight: 600;
            margin: 0 0 5px;
            color: #1e293b;
        }

        .activity-meta {
            font-size: 13px;
            color: #64748b;
            display: flex;
            align-items: center;
        }

        .activity-meta i {
            margin-right: 5px;
            font-size: 12px;
        }

        /* Footer */
        .footer {
            background-color: #f1f5f9;
            padding: 25px 40px;
            text-align: center;
            font-size: 13px;
            color: #64748b;
            border-top: 1px solid #e2e8f0;
        }

        /* Print Button */
        .print-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            color: white;
            border: none;
            border-radius: 12px;
            width: 60px;
            height: 60px;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .print-btn:hover {
            transform: scale(1.1) translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .profile-card {
                flex-direction: column;
                text-align: center;
                margin: -60px 20px 30px;
                padding: 25px;
            }

            .avatar {
                margin-right: 0;
                margin-bottom: 20px;
            }

            .profile-meta {
                justify-content: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
                padding: 0 20px 20px;
            }

            .grid-2col {
                grid-template-columns: 1fr;
            }

            .section {
                padding: 0 20px 30px;
            }

            .header {
                padding: 30px 20px 80px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-content">
                <h1 class="header-title">Sales Representative Profile</h1>
                <p class="header-subtitle">Performance overview • Generated on {{ now()->format('F j, Y') }}</p>
            </div>
            <div class="print-only" style="display: none;">
                <p>Confidential Document</p>
            </div>
        </div>

        <!-- Profile Card -->
        <div class="profile-card">
            <div class="avatar">
                {{ strtoupper(substr($salesRep->name, 0, 1)) }}
            </div>
            <div class="profile-info">
                <h2 class="profile-name">{{ $salesRep->name }}</h2>
                <p class="profile-title">Sales Representative</p>
                <div class="profile-meta">
                    <div class="meta-item">
                        <i class="fas fa-id-badge"></i>
                        ID: {{ $salesRep->id }}
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        Joined: {{ $salesRep->start_work_date->format('M d, Y') }}
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        Tenure: {{ $salesRep->work_duration }} months
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card green">
                <h3 class="stat-title"><i class="fas fa-bullseye"></i> Target Customers</h3>
                <p class="stat-value">{{ $salesRep->target_customers }}</p>
                <p class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> 12% from last month
                </p>
            </div>

            <div class="stat-card blue">
                <h3 class="stat-title"><i class="fas fa-clock"></i> Late Customers</h3>
                <p class="stat-value">{{ $salesRep->late_customers }}</p>
                <p class="stat-change negative">
                    <i class="fas fa-arrow-down"></i> 8% from last month
                </p>
            </div>

            <div class="stat-card purple">
                <h3 class="stat-title"><i class="fas fa-shopping-cart"></i> Total Orders</h3>
                <p class="stat-value">{{ $salesRep->total_orders }}</p>
                <p class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> 15% from last month
                </p>
            </div>

            <div class="stat-card orange">
                <h3 class="stat-title"><i class="fas fa-hourglass-half"></i> Pending Orders</h3>
                <p class="stat-value">{{ $salesRep->pending_orders }}</p>
                <p class="stat-change positive">
                    <i class="fas fa-arrow-down"></i> 5% from last month
                </p>
            </div>

            <div class="stat-card red">
                <h3 class="stat-title"><i class="fas fa-handshake"></i> Interested Customers</h3>
                <p class="stat-value">{{ $salesRep->interested_customers }}</p>
                <p class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> 18% from last month
                </p>
            </div>

            <div class="stat-card indigo">
                <h3 class="stat-title"><i class="fas fa-percentage"></i> Conversion Rate</h3>
                <p class="stat-value">
                    @php
                        $conversionRate = $salesRep->total_orders > 0 ?
                            ($salesRep->target_customers > 0 ? round(($salesRep->total_orders / $salesRep->target_customers) * 100) : 0) :
                            0;
                    @endphp
                    {{ $conversionRate }}%
                </p>
                <p class="stat-change positive">
                    <i class="fas fa-arrow-up"></i> 3% from last month
                </p>
            </div>
        </div>

        <!-- Performance Section -->
        <div class="section">
            <h3 class="section-title"><i class="fas fa-chart-line"></i> Performance Analytics</h3>

            <div class="grid-2col">
                <div class="chart-container">
                    <h4 class="chart-title"><i class="fas fa-chart-pie"></i> Customer Distribution</h4>
                    <div class="chart-placeholder">
                        Customer Distribution Chart
                    </div>
                </div>

                <div class="chart-container">
                    <h4 class="chart-title"><i class="fas fa-chart-bar"></i> Monthly Performance</h4>
                    <div class="chart-placeholder">
                        Monthly Performance Chart
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="section">
            <h3 class="section-title"><i class="fas fa-history"></i> Recent Activity</h3>

            <div class="chart-container">
                <ul class="activity-list">
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="activity-content">
                            <h4 class="activity-title">New order from Acme Corp</h4>
                            <div class="activity-meta">
                                <i class="fas fa-clock"></i> 2 hours ago • $1,250.00
                            </div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <div class="activity-content">
                            <h4 class="activity-title">Meeting with potential client</h4>
                            <div class="activity-meta">
                                <i class="fas fa-clock"></i> Yesterday • 45 minutes
                            </div>
                        </div>
                    </li>
                    <li class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <div class="activity-content">
                            <h4 class="activity-title">Achieved monthly target</h4>
                            <div class="activity-meta">
                                <i class="fas fa-clock"></i> 3 days ago • 112% of goal
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>© {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p class="print-only" style="display: none;">Document generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        </div>
    </div>

    <!-- Print Button -->
    <button class="print-btn no-print" onclick="window.print()">
        <i class="fas fa-print"></i>
    </button>

    <script>
        // Simple script to enhance the PDF (will be executed by Browsershot)
        document.addEventListener('DOMContentLoaded', function() {
            // Add animation class to stat cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
            });

            // Print button functionality
            const printBtn = document.querySelector('.print-btn');
            if (printBtn) {
                printBtn.addEventListener('click', function() {
                    window.print();
                });
            }
        });
    </script>
</body>
</html>
