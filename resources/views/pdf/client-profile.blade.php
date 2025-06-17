<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

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
            font-family: 'Inter', sans-serif;
            color: var(--gray-900);
            background-color: #f9fafb;
            margin: 0;
            padding: 40px;
            line-height: 1.5;
        }

        .document {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            padding: 40px;
            color: white;
            display: flex;
            align-items: center;
            position: relative;
        }

        .header-content {
            flex: 1;
        }

        .logo-container {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-left: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .logo {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .company-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .document-title {
            font-size: 16px;
            font-weight: 500;
            opacity: 0.9;
            letter-spacing: 0.5px;
        }

        .content {
            padding: 40px;
        }

        .section {
            margin-bottom: 32px;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 20px;
            padding-bottom: 8px;
            border-bottom: 2px solid var(--gray-100);
            display: flex;
            align-items: center;
        }

        .section-title:before {
            content: "";
            display: inline-block;
            width: 8px;
            height: 20px;
            background: var(--primary);
            margin-right: 12px;
            border-radius: 4px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 16px;
        }

        .info-item {
            display: contents;
        }

        .label {
            font-weight: 500;
            color: var(--gray-500);
            padding: 8px 0;
        }

        .value {
            padding: 8px 0;
            font-weight: 400;
        }

        .status {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }

        .status-high {
            background-color: #ecfdf5;
            color: #059669;
        }

        .status-medium {
            background-color: #fffbeb;
            color: #d97706;
        }

        .status-low {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid var(--gray-200);
            font-size: 12px;
            color: var(--gray-500);
            text-align: center;
        }

        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-top: 24px;
        }

        .stat-card {
            background: var(--gray-100);
            border-radius: 8px;
            padding: 16px;
            text-align: center;
        }

        .stat-value {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 14px;
            color: var(--gray-500);
        }
    </style>
</head>
<body>
    <div class="document">
        <div class="header">
            <div class="header-content">
                <h1 class="company-name">{{ $client->company_name }}</h1>
                <div class="document-title">Client Profile</div>
            </div>

            <div class="logo-container">
                @if($client->company_logo)
                    <img src="{{ Storage::disk('public')->url($client->company_logo) }}" class="logo">
                @else
                    <div style="color: var(--gray-500); font-size: 14px;">No Logo</div>
                @endif
            </div>
        </div>

        <div class="content">
            <div class="section">
                <h2 class="section-title">Company Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">Address</div>
                        <div class="value">{{ $client->address }}</div>
                    </div>

                    <div class="info-item">
                        <div class="label">Contact Person</div>
                        <div class="value">{{ $client->contact_person }}</div>
                    </div>

                    <div class="info-item">
                        <div class="label">Position</div>
                        <div class="value">{{ $client->contact_position }}</div>
                    </div>

                    <div class="info-item">
                        <div class="label">Phone</div>
                        <div class="value">{{ $client->phone }}</div>
                    </div>

                    <div class="info-item">
                        <div class="label">WhatsApp</div>
                        <div class="value">{{ $client->whatsapp_link ?: 'N/A' }}</div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2 class="section-title">Sales Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="label">Interest Status</div>
                        <div class="value">
                            <span class="status status-{{ $client->interest_status }}">
                                {{ ucfirst($client->interest_status) }}
                            </span>
                        </div>
                    </div>

                    <div class="info-item">
                        <div class="label">Sales Representative</div>
                        <div class="value">{{ $client->salesRep->name ?? 'Not assigned' }}</div>
                    </div>
                </div>

                <div class="stats-container">
                    <div class="stat-card">
                        <div class="stat-value">{{ $client->last_contact_date ? $client->last_contact_date->format('M d, Y') : 'Never' }}</div>
                        <div class="stat-label">Last Contact</div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-value">{{ $client->contact_count }}</div>
                        <div class="stat-label">Total Contacts</div>
                    </div>
                </div>
            </div>

            <div class="footer">
                Generated on {{ now()->format('M d, Y') }} | {{ config('app.name') }}
            </div>
        </div>
    </div>
</body>
</html>
