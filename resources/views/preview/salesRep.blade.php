<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales Rep Preview - {{ $salesRep->name }}</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --secondary: #10b981;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-500: #6b7280;
            --gray-700: #374151;
            --gray-900: #111827;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--gray-900);
            background-color: #f9fafb;
            margin: 0;
            padding: 0;
            line-height: 1.5;
        }

        .preview-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px;
        }

        .preview-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--gray-200);
        }

        .preview-title {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            margin: 0;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary {
            background-color: white;
            color: var(--primary);
            border: 1px solid var(--gray-200);
        }

        .btn-secondary:hover {
            background-color: var(--gray-100);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        .preview-content {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            border: 1px solid var(--gray-200);
        }

        .pdf-container {
            width: 100%;
            height: 1000px;
            border: none;
        }

        .loading-overlay {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 300px;
            background-color: var(--gray-100);
            border-radius: 12px;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top-color: var(--primary);
            animation: spin 1s ease-in-out infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .loading-text {
            font-size: 16px;
            color: var(--gray-700);
            font-weight: 500;
        }

        .tab-container {
            margin-top: 30px;
        }

        .tabs {
            display: flex;
            border-bottom: 1px solid var(--gray-200);
            margin-bottom: 20px;
        }

        .tab {
            padding: 12px 20px;
            cursor: pointer;
            font-weight: 500;
            color: var(--gray-500);
            border-bottom: 3px solid transparent;
            transition: all 0.2s ease;
        }

        .tab.active {
            color: var(--primary);
            border-bottom-color: var(--primary);
        }

        .tab:hover:not(.active) {
            color: var(--gray-700);
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        .stats-preview {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .stat-preview-card {
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--gray-200);
        }

        .stat-preview-title {
            font-size: 14px;
            color: var(--gray-500);
            margin-bottom: 10px;
            font-weight: 500;
        }

        .stat-preview-value {
            font-size: 24px;
            font-weight: 700;
            color: var(--gray-900);
            margin-bottom: 5px;
        }

        .stat-preview-change {
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .positive {
            color: #10b981;
        }

        .negative {
            color: #ef4444;
        }

        .chart-preview-container {
            margin-top: 30px;
            background-color: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--gray-200);
        }

        .chart-preview {
            height: 300px;
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="preview-header">
            <h1 class="preview-title">Sales Representative Profile Preview</h1>
            <div class="action-buttons">
                <button class="btn btn-secondary" onclick="window.history.back()">
                    <i class="fas fa-arrow-left"></i> Back
                </button>
                <a href="{{ route('salesreps.export.pdf', $salesRep->id) }}" class="btn btn-primary">
                    <i class="fas fa-download"></i> Download PDF
                </a>
            </div>
        </div>

        <div class="preview-content">
            <div class="tabs">
                <div class="tab active" data-tab="preview">PDF Preview</div>
                <div class="tab" data-tab="stats">Key Statistics</div>
                <div class="tab" data-tab="charts">Performance Charts</div>
            </div>

            <div class="tab-content active" id="preview-tab">
                <div class="loading-overlay" id="loading-pdf">
                    <div class="spinner"></div>
                    <div class="loading-text">Generating PDF Preview...</div>
                </div>
                <iframe class="pdf-container" id="pdf-preview" style="display: none;"
                        src="{{ route('salesreps.preview.pdf', $salesRep->id) }}"></iframe>
            </div>

            <div class="tab-content" id="stats-tab">
                <h3>Key Performance Indicators</h3>
                <div class="stats-preview">
                    <div class="stat-preview-card">
                        <sales-reps.pdf-previewdiv class="stat-preview-title">Target Customers</div>
                        <div class="stat-preview-value">{{ $salesRep->target_customers }}</div>
                        <div class="stat-preview-change positive">
                            <i class="fas fa-arrow-up"></i> 12% from last month
                        </div>
                    </div>

                    <div class="stat-preview-card">
                        <div class="stat-preview-title">Late Customers</div>
                        <div class="stat-preview-value">{{ $salesRep->late_customers }}</div>
                        <div class="stat-preview-change negative">
                            <i class="fas fa-arrow-down"></i> 8% from last month
                        </div>
                    </div>

                    <div class="stat-preview-card">
                        <div class="stat-preview-title">Total Orders</div>
                        <div class="stat-preview-value">{{ $salesRep->clientRequest->count() }}</div>
                        <div class="stat-preview-change positive">
                            <i class="fas fa-arrow-up"></i> 15% from last month
                        </div>
                    </div>

                    <div class="stat-preview-card">
                        <div class="stat-preview-title">Pending Orders</div>
                        <div class="stat-preview-value">{{ $salesRep->pendedRequest->count() }}</div>
                        <div class="stat-preview-change positive">
                            <i class="fas fa-arrow-down"></i> 5% from last month
                        </div>
                    </div>

                    <div class="stat-preview-card">
                        <div class="stat-preview-title">Interested Customers</div>
                        <div class="stat-preview-value">{{ $salesRep->interested_customers }}</div>
                        <div class="stat-preview-change positive">
                            <i class="fas fa-arrow-up"></i> 18% from last month
                        </div>
                    </div>

                </div>
            </div>

            <div class="tab-content" id="charts-tab">
                <h3>Performance Analytics</h3>

                <div class="chart-preview-container">
                    <h4>Monthly Performance</h4>
                    <div class="chart-preview">
                        <canvas id="monthlyChart"></canvas>
                    </div>
                </div>

                <div class="chart-preview-container" style="margin-top: 20px;">
                    <h4>Customer Distribution</h4>
                    <div class="chart-preview">
                        <canvas id="customerChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab functionality
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

                tab.classList.add('active');
                document.getElementById(`${tab.dataset.tab}-tab`).classList.add('active');
            });
        });

        // Show PDF when loaded
        document.getElementById('pdf-preview').onload = function() {
            document.getElementById('loading-pdf').style.display = 'none';
            document.getElementById('pdf-preview').style.display = 'block';
        };

        // Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Performance Chart
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'Sales',
                        data: [12000, 19000, 15000, 22000, 18000, 25000],
                        backgroundColor: 'rgba(79, 70, 229, 0.7)',
                        borderColor: 'rgba(79, 70, 229, 1)',
                        borderWidth: 1
                    }, {
                        label: 'Target',
                        data: [15000, 15000, 15000, 15000, 15000, 15000],
                        backgroundColor: 'rgba(16, 185, 129, 0.5)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1,
                        type: 'line',
                        fill: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });

            // Customer Distribution Chart
            const customerCtx = document.getElementById('customerChart').getContext('2d');
            const customerChart = new Chart(customerCtx, {
                type: 'doughnut',
                data: {
                    labels: ['New Customers', 'Repeat Customers', 'Potential Customers', 'Inactive Customers'],
                    datasets: [{
                        data: [35, 25, 20, 20],
                        backgroundColor: [
                            'rgba(79, 70, 229, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(245, 158, 11, 0.7)',
                            'rgba(239, 68, 68, 0.7)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                        }
                    }
                }
            });
        });
    </script>
</body>
</html>
