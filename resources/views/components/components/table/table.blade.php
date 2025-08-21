<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reusable RTL Table Component</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --secondary: #10b981;
            --danger: #ef4444;
            --warning: #f59e0b;
            --gray-100: #f8fafc;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Tajawal', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--gray-100);
            color: var(--gray-800);
            direction: rtl;
            padding: 20px;
        }

        .table-component {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            margin-bottom: 30px;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            border-bottom: 1px solid var(--gray-200);
        }

        .table-title {
            position: relative;
            display: inline-block;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            font-size: 1.5rem;
            font-weight: 700;
            color: #1f2937;
            /* gray-800 */
        }

        .dark .table-title {
            color: #e5e7eb;
            /* gray-200 */
        }

        .table-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            width: 33%;
            height: 3px;
            background: #3b82f6;
            border-radius: 9999px;
            transition: all 0.3s ease;
        }

        .table-title:hover::after {
            width: 100%;
        }

        .table-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 500;
            font-size: 14px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            font-family: 'Tajawal', sans-serif;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--primary-light);
            transform: translateY(-1px);
        }

        .btn-outline {
            background-color: white;
            color: var(--primary);
            border: 1px solid var(--gray-300);
        }

        .btn-outline:hover {
            background-color: var(--gray-100);
        }

        .table-filters {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 20px;
            background-color: var(--gray-100);
            border-bottom: 1px solid var(--gray-200);
        }

        .search-box {
            position: relative;
            width: 300px;
        }

        .search-input {
            width: 100%;
            padding: 8px 40px 8px 15px;
            border-radius: 6px;
            border: 1px solid var(--gray-300);
            font-family: 'Tajawal', sans-serif;
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }

        .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .data-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 800px;
        }

        .data-table thead th {
            background-color: var(--gray-100);
            color: var(--gray-600);
            font-weight: 600;
            padding: 12px 15px;
            text-align: right;
            border-bottom: 2px solid var(--gray-200);
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .data-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .data-table tbody tr:hover {
            background-color: var(--gray-100);
        }

        .data-table tbody td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--gray-200);
            text-align: right;
            vertical-align: middle;
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-active {
            background-color: #ecfdf5;
            color: #059669;
        }

        .status-pending {
            background-color: #fffbeb;
            color: #d97706;
        }

        .status-inactive {
            background-color: #fee2e2;
            color: #dc2626;
        }

        .action-btns {
            display: flex;
            gap: 8px;
            justify-content: flex-start;
        }

        .action-btn {
            width: 30px;
            height: 30px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: transparent;
            border: none;
            color: var(--gray-500);
        }

        .action-btn:hover {
            background-color: var(--gray-200);
            color: var(--gray-700);
        }

        .action-btn.edit:hover {
            color: var(--primary);
        }

        .action-btn.delete:hover {
            color: var(--danger);
        }

        .pagination {
            display: flex;
            justify-content: center;
            padding: 20px;
            border-top: 1px solid var(--gray-200);
        }

        .pagination-btn {
            width: 36px;
            height: 36px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 4px;
            cursor: pointer;
            transition: all 0.2s ease;
            background-color: transparent;
            border: 1px solid var(--gray-300);
            color: var(--gray-600);
            font-family: 'Tajawal', sans-serif;
        }

        .pagination-btn:hover:not(.active):not(.disabled) {
            background-color: var(--gray-100);
            border-color: var(--gray-400);
        }

        .pagination-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .pagination-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: var(--gray-500);
        }

        .empty-icon {
            font-size: 48px;
            color: var(--gray-300);
            margin-bottom: 15px;
        }

        .empty-text {
            font-size: 16px;
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .table-actions {
                width: 100%;
                justify-content: flex-end;
            }

            .search-box {
                width: 100%;
            }
        }
    </style>
</head>
@extends('partials.header')

<body>
    <div class="table-responsive">
        <table class="data-table w-full text-right border-collapse">
          <thead class="bg-gray-100 dark:bg-gray-800">
    <tr>
        <!-- اسم المندوب -->
        <th class="px-4 py-2 text-sm font-bold text-gray-700 dark:text-gray-200 text-right">
            اسم سفير العلامة التجارية
        </th>

        <!-- تاريخ الالتحاق بالعمل -->
        <th class="px-4 py-2 text-sm font-bold text-gray-700 dark:text-gray-200 text-right">
            تاريخ الالتحاق بالعمل
        </th>

        <!-- مدة العمل -->
        <th class="px-4 py-2 text-sm font-bold text-gray-700 dark:text-gray-200 text-right">
            مدة العمل
        </th>

        <!-- عدد العملاء المستهدفين -->
        <th class="px-4 py-2 text-sm font-bold text-gray-700 dark:text-gray-200 text-right">
            عدد العملاء المستهدفين
        </th>

        <!-- عدد العملاء المتأخرين -->
        <th class="px-4 py-2 text-sm font-bold text-gray-700 dark:text-gray-200 text-right">
            عدد العملاء المتأخرين
        </th>

        <!-- عدد الطلبات الإجمالية -->
        <th class="px-4 py-2 text-sm font-bold text-gray-700 dark:text-gray-200 text-right">
            عدد الطلبات الإجمالية
        </th>

        <!-- عدد الطلبات المعلقة -->
        <th class="px-4 py-2 text-sm font-bold text-gray-700 dark:text-gray-200 text-right">
            عدد الطلبات المعلقة
        </th>

        <!-- عدد العملاء المهتمين والمحتملين -->
        <th class="px-4 py-2 text-sm font-bold text-gray-700 dark:text-gray-200 text-right">
            عدد العملاء المهتمين والمحتملين
        </th>
    </tr>
</thead>
            <tbody id="clients-table-body">
                {{ $slot }} {{-- This is how you pass dynamic row content --}}
            </tbody>
        </table>
    </div>

    <div class="pagination" id="clients-pagination">
        <!-- Pagination will be injected here by JavaScript -->
    </div>

</body>
<script>
    // Render pagination
        function renderPagination(elementId, currentPage, totalPages) {
            const pagination = document.getElementById(elementId);
            pagination.innerHTML = '';

            if (totalPages <= 1) return;

            // Previous button
            pagination.innerHTML += `
                <button class="pagination-btn ${currentPage === 1 ? 'disabled' : ''}"
                        onclick="changePage('${elementId}', ${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
                    <i class="fas fa-chevron-right"></i>
                </button>
            `;

            // Page numbers
            for (let i = 1; i <= totalPages; i++) {
                pagination.innerHTML += `
                    <button class="pagination-btn ${currentPage === i ? 'active' : ''}"
                            onclick="changePage('${elementId}', ${i})">
                        ${i}
                    </button>
                `;
            }

            // Next button
            pagination.innerHTML += `
                <button class="pagination-btn ${currentPage === totalPages ? 'disabled' : ''}"
                        onclick="changePage('${elementId}', ${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
                    <i class="fas fa-chevron-left"></i>
                </button>
            `;
        }

        // Placeholder functions for actions
        function changePage(elementId, page) {
            console.log(`Changing to page ${page} for ${elementId}`);
            // In a real app, you would fetch data for the new page
        }

        function searchClients(query) {
            console.log(`Searching clients for: ${query}`);
            const filtered = clientsData.filter(client =>
                client.name.includes(query) ||
                client.email.includes(query) ||
                client.phone.includes(query)
            );
            renderClientsTable(filtered);
        }


        function addNewClient() {
            console.log("Adding new client");
        }

        function editClient(id) {
            console.log(`Editing client with ID: ${id}`);
        }

        function deleteClient(id) {
            console.log(`Deleting client with ID: ${id}`);
        }

        function viewClient(id) {
            console.log(`Viewing client with ID: ${id}`);
        }


        function exportClients() {
            console.log("Exporting clients data");
        }

        function exportAgreements() {
            console.log("Exporting agreements data");
        }

        function showFilters() {
            console.log("Showing client filters");
        }

        // Initialize tables when page loads
        document.addEventListener('DOMContentLoaded', function() {
            renderClientsTable();
        });
</script>

</html>
