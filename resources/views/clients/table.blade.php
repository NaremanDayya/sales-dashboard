@extends('layouts.master')
@section('title','جدول العملاء')
@push('styles')
    <style>
        :root {
            --primary: #4f46e5;
            --primary-light: #6366f1;
            --secondary: #10b981;
            --danger: #ef4444;
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
            font-family: 'Tajawal', sans-serif;
        }

        body {
            background-color: var(--gray-100);
            color: var(--gray-800);
            direction: rtl;
            padding: 20px;
        }
        .date-filter {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .date-filter input[type="date"] {
            height: 38px;
            border: 1px solid var(--gray-300);
            border-radius: 6px;
            padding: 0 12px;
            font-family: 'Tajawal', sans-serif;
        }
        .clickable-cell {
            transition: background-color 0.2s ease, color 0.2s ease;
        }
        .clickable-cell:hover {
            background-color: #f1f5f9; /* light hover background */
            color: #4154f1; /* your brand blue */
            text-decoration: underline; /* shows it's clickable */
        }
        .position-relative {
            position: relative;
        }
        .hidden {
            display: none !important;
        }

        #clientEditModal {
            display: none;
        }

        #clientEditModal:not(.hidden) {
            display: flex !important;
        }
        .position-absolute {
            position: absolute;
        }
        .edit-dropdown {
            position: relative;
            display: inline-block;
        }

        .edit-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            z-index: 10;
            min-width: 120px;
        }

        .edit-menu-item {
            padding: 8px 12px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.2s;
        }

        .edit-menu-item:hover {
            background-color: #f9fafb;
        }

        .edit-dropdown:hover .edit-menu {
            display: block;
        }

        /* Make cells clickable for redirect */
        .clickable-cell {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .clickable-cell:hover {
            background-color: #f1f5f9;
        }

        .top-0 {
            top: 0;
        }

        .start-0 {
            left: 0;
        }

        .translate-middle {
            transform: translate(-50%, -50%);
        }

        .badge {
            display: inline-block;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.375rem;
        }

        .bg-primary {
            background-color: var(--primary);
            color: white;
        }

        .rounded-pill {
            border-radius: 50rem !important;
        }
        /* Replace the .table-filters and related styles with these */

        .table-filters {
            display: flex;
            flex-direction: column;
            gap: 15px;
            padding: 15px 20px;
            background-color: var(--gray-100);
            border-bottom: 1px solid var(--gray-200);
        }

        .filter-row {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            width: 100%;
        }

        /* First row - search on right, other filters on left */
        .filter-row:first-child {
            justify-content: space-between;
        }

        .search-box {
            position: relative;
            width: 250px;
            margin-left: auto; /* Push search to right */
        }

        /* Second row - date filters on both sides */
        .filter-row:last-child {
            justify-content: space-between;
        }

        /* Left side date filter group */
        .filter-row:last-child .filter-group:first-child {
            margin-right: auto; /* Push to left */
        }

        /* Right side date filter group */
        .filter-row:last-child .filter-group:last-child {
            margin-left: auto; /* Push to right */
        }

        /* Search input RTL adjustment */
        .search-input {
            width: 100%;
            padding: 8px 15px 8px 40px; /* Adjust padding for RTL */
            border-radius: 6px;
            border: 1px solid var(--gray-300);
            font-size: 14px;
            transition: all 0.2s ease;
            text-align: right; /* RTL text alignment */
        }

        .search-icon {
            position: absolute;
            right: 15px; /* Changed from left to right for RTL */
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
        }

        /* Filter groups alignment */
        .filter-group {
            display: flex;
            align-items: center;
        }

        /* Date input specific styles */
        .date-input {
            padding: 8px 12px;
            border-radius: 6px;
            border: 1px solid var(--gray-300);
            direction: rtl;
            text-align: right;
            min-width: 120px;
            font-size: 14px;
        }

        .date-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }

        .filter-btn {
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 14px;
            min-width: auto;
        }

        .count-badge {
            display: none;
            font-size: 10px;
            padding: 3px 6px;
            background-color: var(--primary);
            color: white;
        }

        /* Responsive adjustments */
        @media (max-width: 1200px) {
            .filter-row {
                gap: 10px;
            }

            .search-box {
                width: 200px;
            }

            .date-input {
                min-width: 100px;
            }
        }

        @media (max-width: 768px) {
            .table-filters {
                padding: 10px;
            }

            .filter-row {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }

            .search-box {
                width: 100%;
                margin-left: 0;
                order: -1; /* Move search to top on mobile */
            }

            .filter-group {
                width: 100%;
                justify-content: center;
            }

            .filter-row:last-child .filter-group:first-child,
            .filter-row:last-child .filter-group:last-child {
                margin-right: 0;
                margin-left: 0;
            }

            .form-select, .date-input {
                flex: 1;
                min-width: auto;
            }
        }

        /* Button styles */
        .btn-outline {
            background-color: white;
            color: var(--primary);
            border: 1px solid var(--gray-300);
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .btn-outline:hover {
            background-color: var(--gray-100);
            border-color: var(--primary);
        }

        /* Icon styles */
        .text-secondary {
            color: var(--gray-500);
        }

        /* Position utilities */
        .position-relative {
            position: relative;
        }

        .position-absolute {
            position: absolute;
        }
        #logoPreview {
            max-width: 100%;
            height: auto;
            object-fit: contain;
        }

        #logoPreviewContainer {
            text-align: center;
            border: 1px dashed #d1d5db;
            border-radius: 0.375rem;
            padding: 1rem;
            background-color: #f9fafb;
        }

        .top-0 {
            top: 0;
        }

        .start-0 {
            left: 0;
        }

        .translate-middle {
            transform: translate(-50%, -50%);
        }

        .rounded-pill {
            border-radius: 50rem;
        }

        /* Flex utilities */
        .d-flex {
            display: flex;
        }

        .align-items-center {
            align-items: center;
        }

        .gap-2 {
            gap: 8px;
        }

        .gap-3 {
            gap: 12px;
        }

        /* Button styles */
        .btn-outline {
            background-color: white;
            color: var(--primary);
            border: 1px solid var(--gray-300);
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s ease;
            font-size: 14px;
        }

        .btn-outline:hover {
            background-color: var(--gray-100);
            border-color: var(--primary);
        }

        /* Icon styles */
        .text-secondary {
            color: var(--gray-500);
        }

        /* Position utilities */
        .position-relative {
            position: relative;
        }

        .position-absolute {
            position: absolute;
        }

        .top-0 {
            top: 0;
        }

        .start-0 {
            left: 0;
        }

        .translate-middle {
            transform: translate(-50%, -50%);
        }

        .rounded-pill {
            border-radius: 50rem;
        }

        /* Flex utilities */
        .d-flex {
            display: flex;
        }

        .align-items-center {
            align-items: center;
        }

        .gap-2 {
            gap: 8px;
        }

        .gap-3 {
            gap: 12px;
        }
        .date-filter input[type="date"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(79, 70, 229, 0.2);
        }
        .table-html {
            width: 100%;
        }
        .main, #main {
            flex: 1 0 auto !important;
            padding-bottom: 80px !important;
        }
        .table-html.no-sidebar #main-table {
            margin-right: 0 !important;
            width: 100% !important;
        }


        .edit-input:focus {
            outline: none;
            border-color: var(--primary-light);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
        }


        .edit-btn {
            padding: 8px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            transition: all 0.2s ease;
        }


        .edit-save:hover {
            background-color: #0da271;
            transform: translateY(-1px);
        }

        .editable-cell {
            position: relative;
            cursor: pointer;
        }

        .editable-cell:hover {
            background-color: #f9fafb;
        }

        .edit-icon {
            opacity: 0;
            transition: opacity 0.2s;
            margin-right: 5px;
            color: #6b7280;
        }

        .editable-cell:hover .edit-icon {
            opacity: 1;
        }

        .edit-form {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .edit-input {
            flex: 1;
            padding: 4px 8px;
            border: 1px solid #d1d5db;
            border-radius: 4px;
            font-size: 0.875rem;
        }

        .edit-actions {
            display: flex;
            gap: 4px;
        }

        .edit-btn {
            padding: 4px 8px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.875rem;
        }

        .edit-save {
            background-color: #10b981;
            color: white;
        }

        .edit-cancel {
            background-color: #ef4444;
            color: white;
        }

        .edit-btn:hover {
            opacity: 0.9;
        }

        .edit-cancel:hover {
            background-color: var(--gray-400);
            transform: translateY(-1px);
        }



        /* Notification styles */
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            z-index: 10000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .notification.success {
            background-color: var(--secondary);
            border-left: 4px solid #059669;
        }

        .notification.error {
            background-color: var(--danger);
            border-left: 4px solid #dc2626;
        }

        .notification.fade-out {
            animation: fadeOut 0.5s ease forwards;
        }

        @keyframes fadeOut {
            from {
                opacity: 1;
                transform: translateX(0);
            }
            to {
                opacity: 0;
                transform: translateX(100%);
            }
        }

        /* Specific styles for different field types */
        .edit-form input[type="tel"] {
            direction: ltr;
            text-align: left;
        }

        .edit-form select {
            padding: 8px 12px;
            border: 2px solid var(--primary);
            border-radius: 6px;
            font-size: 14px;
            font-weight: 700;
            width: 100%;
            background-color: white;
            cursor: pointer;
        }

        .export-btn-group {
            position: relative;
            display: inline-block;
        }

        .export-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border-radius: 6px;
            background-color: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .export-btn:hover {
            background-color: var(--primary-light);
            transform: translateY(-1px);
        }

        .columns-badge {
            background-color: white;
            color: var(--primary);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: bold;
        }

        .columns-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .columns-modal-content {
            background-color: white;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .columns-modal-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .columns-modal-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--gray-800);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .columns-modal-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: var(--gray-500);
            transition: color 0.2s;
        }

        .columns-modal-close:hover {
            color: var(--danger);
        }

        .columns-modal-body {
            padding: 15px;
            overflow-y: auto;
            flex-grow: 1;
        }

        .columns-search {
            position: relative;
            margin-bottom: 15px;
        }

        .columns-search input {
            width: 100%;
            padding: 10px 15px 10px 40px;
            border-radius: 6px;
            border: 1px solid var(--gray-300);
            font-size: 14px;
        }

        .columns-search i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--gray-400);
        }

        .columns-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .column-item {
            padding: 5px 0;
        }

        .column-checkbox {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 8px 10px;
            border-radius: 6px;
            transition: background-color 0.2s;
        }

        .column-checkbox:hover {
            background-color: var(--gray-100);
        }

        .column-checkbox input {
            display: none;
        }

        .checkmark {
            width: 18px;
            height: 18px;
            border: 2px solid var(--gray-300);
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .column-checkbox input:checked~.checkmark {
            background-color: var(--primary);
            border-color: var(--primary);
        }

        .checkmark:after {
            content: "✓";
            color: white;
            font-size: 12px;
            display: none;
        }

        .column-checkbox input:checked~.checkmark:after {
            display: block;
        }

        .column-name {
            font-size: 14px;
            color: var(--gray-700);
        }

        .columns-modal-footer {
            padding: 15px 20px;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .columns-actions {
            display: flex;
            gap: 10px;
        }

        .btn-select-all {
            background: none;
            border: none;
            color: var(--primary);
            font-size: 14px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .btn-select-all:hover {
            background-color: var(--gray-100);
        }

        .btn-cancel {
            padding: 8px 16px;
            border-radius: 6px;
            background-color: white;
            color: var(--gray-700);
            border: 1px solid var(--gray-300);
            cursor: pointer;
            font-size: 14px;
            margin-left: 10px;
            transition: all 0.2s;
        }

        .btn-cancel:hover {
            background-color: var(--gray-100);
        }

        .btn-apply {
            padding: 8px 16px;
            border-radius: 6px;
            background-color: var(--primary);
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s;
        }

        .btn-apply:hover {
            background-color: var(--primary-light);
        }

        .nice-button:hover {
            background-color: #334ae2;
        }

        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-select {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 300px;
            box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            margin-top: 5px;
        }

        .dropdown-select option {
            padding: 8px 12px;
            cursor: pointer;
        }

        .dropdown-select option:hover {
            background-color: #e9e9e9;
        }

        .btn-dropdown {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            color: #374151;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-dropdown:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        .btn-dropdown svg {
            transition: transform 0.2s;
        }

        .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            min-width: 180px;
            margin-top: 4px;
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            z-index: 10;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            width: 100%;
            padding: 8px 12px;
            text-align: left;
            color: #374151;
            font-size: 14px;
            background: none;
            border: none;
            cursor: pointer;
        }

        .dropdown-item:hover {
            background: #f9fafb;
        }

        .dropdown-icon {
            color: #6b7280;
        }

        /* Show dropdown when active */
        .dropdown.active .btn-dropdown svg {
            transform: rotate(180deg);
        }

        .dropdown.active .dropdown-menu {
            display: block;
        }

        .table-container {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin: 1rem;
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.25rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
        }

        .table-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 800 !important;
            font-size: 14px !important;
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
            border-collapse: collapse;
            min-width: 800px;
        }

        .data-table thead th {
            background-color: var(--gray-100);
            color: var(--gray-600);
            font-weight: 600;
            padding: 5px 8px !important;
            text-align: center;
            border-bottom: 2px solid var(--gray-200);
            position: sticky;
            text-align: center !important;
            vertical-align: middle !important;
            top: 0;
        }

        .data-table tbody tr {
            transition: background-color 0.2s ease;
            page-break-inside: avoid;
            break-inside: avoid;
        }

        .data-table tbody tr:hover {
            background-color: var(--gray-100);
        }

        .data-table tbody td {
            font-weight: 700;
            font-size: 14px;
            padding: 5px 8px !important;
            border-bottom: 1px solid var(--gray-200);
            text-align: center !important;
            vertical-align: middle !important;
        }
        .data-table td > * {
            justify-content: center !important;
            text-align: center !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }
        .data-table td .flex {
            justify-content: center !important;
        }
        .data-table td img {
            display: block !important;
            margin: 0 auto !important;
        }


        /* Center badges and status indicators */
        .data-table .badge,
        .data-table .status-badge,
        .data-table [class*="bg-"] {
            margin: 0 auto !important;
            display: inline-flex !important;
            justify-content: center !important;
            align-items: center !important;
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
            justify-content: center !important;
            gap: 8px;
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
        .ltr-number {
            direction: ltr;
            unicode-bidi: embed;
            display: inline-block;
        }
        #clientEditModal {
            font-family: 'Tajawal', sans-serif;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        input, select {
            font-family: 'Tajawal', sans-serif;
        }

        /* Scrollbar styling for modal */
        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
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
        }

        .pagination-btn:hover:not(.disabled) {
            background-color: var(--gray-100);
        }

        .pagination-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        [x-cloak] { display: none !important; }

        .pagination-btn.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .sidebar {
            display: none !important;
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

            .search-box {
                width: 100%;
                justify-content:start;
            }
        }

        .print-header,
        .print-footer {
            display: none;
        }

        /* Show only in print/PDF */
        .pdf-header,
        .pdf-footer {
            display: block;
            width: 100%;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
            padding: 10px 0;
            border-bottom: 2px solid #333;
            border-top: 2px solid #333;
            background-color: #f5f5f5;
            color: #222;
            position: relative;
            margin-bottom: 20px;
            margin-top: 20px;
            font-family: "Arial", sans-serif;
        }

        .pdf-footer {
            border-top: 2px solid #333;
            border-bottom: none;
            margin-top: 40px;
        }

        .header-content {
            direction: rtl;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-weight: bold;
            font-size: 20px;
            color: #222;
        }
        .data-table th,
        .data-table td {
            text-align: center !important;
        }

        #clientEditModal {
            font-family: 'Tajawal', sans-serif;
        }

        .form-group {
            margin-bottom: 1rem;
            /* REMOVE display:block override if you had */
        }

        input, select {
            font-family: 'Tajawal', sans-serif;
        }

        /* Scrollbar styling */
        .modal-body {
            max-height: 65vh; /* important */
            overflow-y: auto; /* enable scroll */
        }

        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        .header-text {
            color: #4154f1;
        }

        .header-logo {
            max-height: 60px;
            width: auto;
            object-fit: contain;
        }

        @media print {

            /* Hide everything outside print-area */
            body * {
                visibility: hidden;
            }

            #print-area,
            #print-area * {
                visibility: visible;
            }

            /* Position print-area at top left and full width */
            #print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                padding: 20px;
                background: #fff;
                font-size: 14px;
                color: #000;
            }

            /* Hide UI controls */
            #print-area .table-actions,
            #print-area .table-filters,
            #print-area .pagination,
            #print-area .btn,
            #print-area select,
            #print-area input,
            #print-area .search-box,
            #print-area .search-icon,
            #print-area .fas.fa-plus,
            #print-area .fas.fa-download,
            #print-area .fas.fa-print {
                display: none !important;
            }

            /* Style the table */
            #print-area table.data-table {
                width: 100% !important;
                border-collapse: collapse;
                text-align: center !important;

            }

            #print-area table.data-table th,
            #print-area table.data-table td {
                border: 1px solid #000;
                padding: 8px;
                text-align: center;
            }


            #print-area table.data-table thead {
                background-color: #f0f0f0;
            }

            /* Prevent page breaks inside rows */
            #print-area tr {
                page-break-inside: avoid;
            }

            /* Show footer in print */
            #print-area .pdf-footer {
                display: block !important;
                text-align: center;
                margin-top: 30px;
                font-size: 12px;
                color: #444;
            }

            /* RTL support */
            #print-area {
                direction: rtl;
                width: 100% !important
            }

            .no-print {
                display: none !important;
            }

            .pdf-header {
                display: block !important;
            }

            /* Optional: remove shadows, rounded borders in print for better clarity */
            .pdf-header .header-content {
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .body {
                margin: 0;
                padding: 0;
            }

            .header-logo {
                max-height: 80px;
            }
        }
    </style>
@endpush
@section('favicon')
    <link rel="icon" type="image/png" href="{{ asset('assets/img/favicon.jpg') }}">
    <link rel="shortcut icon" href="{{ asset('assets/img/favicon.jpg') }}">
@endsection
<body>
@section('content')
    <div class="table-html no-sidebar">

        <div id="print-area" class="table-container">
            <div class="table-header">
                <h2 id="title" class="table-title">العملاء</h2>
                <div class="table-actions d-flex align-items-center gap-2">
                    @if(Auth::user()->role == 'salesRep')
                        <a class="btn btn-primary" href="{{ route('sales-reps.clients.create', Auth::user()->salesRep->id) }}">
                            <i class="fas fa-plus"></i> إضافة عميل
                        </a>
                    @endif
                    @if(Auth::user()->role == 'admin')
                        <a class="btn btn-primary" href="{{ route('admin.shared-companies') }}">
                            <i class="fas fa-users"></i> العملاء المشتركين
                        </a>
                    @endif
                    <div class="export-btn-group">
                        <button id="columnsBtn" class="export-btn columns-btn" onclick="openColumnsModal()">
                            <span class="btn-icon"><i class="fas fa-columns"></i></span>
                            <span class="btn-text">اختيار الأعمدة</span>
                            <span id="columnsBadge" class="columns-badge">13</span>
                        </button>
                    </div>
                    <div class="export-options">
                        <div class="dropdown">
                            <button class="btn btn-dropdown" id="exportBtn" type="button">
                                تصدير البيانات
                                <svg width="12" height="8" viewBox="0 0 12 8" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1 1L6 6L11 1" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                </svg>
                            </button>
                            <div class="dropdown-menu" id="exportDropdown">
                                <button class="dropdown-item" data-type="xlsx">
                                    <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path d="M4 6H20M4 12H20M4 18H11" stroke="currentColor" stroke-width="2"
                                              stroke-linecap="round" />
                                    </svg>
                                    تصدير كملف Excel
                                </button>
                                <button class="dropdown-item" data-type="pdf">
                                    <svg class="dropdown-icon" width="16" height="16" viewBox="0 0 24 24" fill="none"
                                         xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M4 7V17C4 18.1046 4.89543 19 6 19H18C19.1046 19 20 18.1046 20 17V7M4 7H20M4 7L6 4H18L20 7"
                                            stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                    </svg>
                                    تصدير كملف PDF
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Columns Selection Modal -->
                    <div id="columnsModal" class="columns-modal">
                        <div class="columns-modal-content">
                            <div class="columns-modal-header">
                                <h3 class="columns-modal-title">
                                    <i class="fas fa-columns"></i>
                                    اختيار الأعمدة للعرض
                                </h3>
                                <button class="columns-modal-close" onclick="closeColumnsModal()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <div class="columns-modal-body">
                                <div class="columns-search">
                                    <input type="text" id="columnsSearch" placeholder="بحث عن عمود..."
                                           onkeyup="filterColumns()">
                                    <i class="fas fa-search"></i>
                                </div>


                                <div class="columns-list" id="columnsList">
                                    <!-- Column items will be generated here -->
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="client_logo" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name">شعار شركة العميل</span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="company_name" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name"> الشركة </span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="address" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name"> مقر الشركة</span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="contact_person" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name"> الشخص المسؤول</span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="contact_position" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name">المنصب الوظيفي </span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="phone" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name">رقم الجوال</span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="whatsapp_link" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name">رابط واتس اب مباشر</span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="interest_status" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name">حالة الاهتمام </span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="interested_service" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name">الخدمة المهتم بها</span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="last_contact_date" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name">تاریخ آخر تواصل </span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="contact_days_left" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name"> آخر تواصل منذ</span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="contact_count" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name"> عدد مرات التواصل </span>
                                        </label>
                                    </div>
                                    <div class="column-item">
                                        <label class="column-checkbox">
                                            <input type="checkbox" value="requests_count" checked>
                                            <span class="checkmark"></span>
                                            <span class="column-name">طلبات العميل </span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="columns-modal-footer">
                                <div class="columns-actions">
                                    <button class="btn-select-all" onclick="toggleSelectAll()">تحديد الكل</button>
                                    <button class="btn-select-all" onclick="resetSelection()">إعادة تعيين</button>
                                </div>
                                <div>
                                    <button class="btn-cancel" onclick="closeColumnsModal()">إلغاء</button>
                                    <button class="btn-apply" onclick="applyColumnSelection()">
                                        <i class="fas fa-check"></i>
                                        تطبيق
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-outline" onclick="window.print()" title="طباعة التقرير">
                        <i class="fas fa-print"></i>
                    </button>
                </div>
            </div>

            <div class="table-filters">
                <div class="filter-row">
                    <div class="search-box">
                        <input type="text" class="search-input" placeholder="بحث..." id="searchInput">
                        <i class="fas fa-search search-icon"></i>
                    </div>
                    <div class="filter-group">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-filter text-secondary"></i>
                            <select id="filterSelect" onchange="applyFilter()" class="form-select" style="font-weight: 700; font-size: 14px; min-width: 120px;">
                                <option value="">الكل</option>
                                <option value="interested">مهتم</option>
                                <option value="not interested">غير مهتم</option>
                                <option value="late">متأخرين</option>
                                <option value="late_interested">متأخرين مهتمين</option>
                                <option value="late_not_interested">متأخرين غير مهتمين</option>
                                <option value="late_neutral">متأخرين مؤجلين</option>
                            </select>
                        </div>
                    </div>

                    <div class="filter-group">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-cog text-secondary"></i>
                            <select id="serviceTypeFilter" onchange="applyFilter()" class="form-select" style="font-size: 14px; font-weight: 800; min-width: 150px;">
                                <option value="">الكل</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="filter-group">
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-user-tie text-secondary"></i>
                            <select id="salesRepFilter" onchange="applyFilter()" class="form-select" style="font-size: 14px; font-weight: 800; min-width: 150px;">
                                <option value="">كل المندوبين</option>
                                @foreach($sales_rep_names as $name)
                                    <option value="{{ $name }}">{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    @if(Auth::user()->role == 'admin')
                        <div class="filter-group">
                            <div x-data="{ open: false }">
                                <button @click="open = true" class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded shadow text-sm">
                                    أيام التأخير
                                </button>
                                <!-- Modal -->
                                <div x-show="open" x-cloak class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
                                    <div @click.away="open = false" class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">

                                        <h3 class="text-lg font-bold mb-4">تعديل عدد الأيام لتأخير العميل</h3>

                                        <form action="{{ route('settings.update') }}" method="POST">
                                            @csrf
                                            <div class="mb-4">
                                                <label for="late_customer_days" class="block text-sm font-medium text-gray-700">عدد الأيام</label>
                                                <input type="number" name="late_customer_days" id="late_customer_days" min="1" max="30" required
                                                       value="{{ old('late_customer_days', \App\Models\Setting::where('key', 'late_customer_days')->value('value') ?? 3) }}"
                                                       class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500">
                                            </div>
                                            <div class="flex justify-end space-x-2">
                                                <button type="button" @click="open = false"
                                                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">إلغاء</button>
                                                <button type="submit"
                                                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">حفظ</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                        <!-- Second Row: Date Filters -->
                        <div class="filter-row">
                            <!-- Left side: Created At Date Filter -->
                            <div class="filter-group pl-10">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="fas fa-calendar-plus text-secondary"></i>
                                    <div class="position-relative">
                                        <input type="text" id="createdAtFilter" class="form-input date-input" placeholder="تاريخ الإنشاء">
                                        <span id="createdAtCount" class="position-absolute top-0 start-0 translate-middle badge bg-primary rounded-pill count-badge">
                    0
                </span>
                                    </div>
                                    <button onclick="filterByCreatedDate()" class="btn btn-outline filter-btn">🔍</button>
                                    <button onclick="resetCreatedDateFilter()" class="btn btn-outline filter-btn">❌</button>
                                </div>
                            </div>

                            <!-- Right side: Contact Date Range Filter -->
                            <div class="filter-group">
                                <div class="d-flex align-items-center gap-2" dir="rtl">
                                    <i class="fas fa-calendar-alt text-secondary"></i>
                                    <input type="text" id="fromDate" class="form-input date-input" placeholder="من تاريخ">
                                    <input type="text" id="toDate" class="form-input date-input" placeholder="إلى تاريخ">
                                    <button onclick="filterByDate()" class="btn btn-outline filter-btn">🔍 تصفية</button>
                                    <button onclick="resetDateFilter()" class="btn btn-outline filter-btn">❌ إعادة</button>
                                </div>
                            </div>
                        </div>
                </div>

                <div class="table-responsive">
                    <div class="pdf-content">
                        <div class="pdf-header" style="display: none;">
                            <div
                                class="header-content d-flex align-items-center justify-content-between flex-wrap mb-4 p-3 shadow rounded bg-white">
                                <div class="d-flex flex-column align-items-center text-center mx-auto">
                                    <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="header-logo mb-2" />
                                    <h2 class="header-text">تقرير العملاء</h2>
                                </div>
                            </div>
                        </div>
                        <table class="data-table">
                            <thead>
                            <tr>
                                <th>شعار شركة العميل</th>
                                <th> الشركة </th>
                                <th>مقر الشركة </th>
                                <th> الشخص المسؤول </th>
                                <th>المنصب الوظيفي </th>
                                <th>رقم الجوال </th>
                                <th class="no-print">واتس اب مباشر </th>
                                <th>حالة الاهتمام </th>
                                <th>الخدمة المهتم بها</th>
                                <th>عدد الخدمة </th>
                                <th>آخر تواصل </th>
                                <th> آخر تواصل منذ</th>
                                <th>عدد مرات التواصل </th>
                                <th>طلبات العميل </th>
                                <th class="no-print">الدردشة</th>
                            </tr>
                            </thead>

                            <tbody id="tableBody">

                            <!-- Data will be inserted here -->
                            </tbody>
                        </table>
                        <div class="pdf-footer" style="display: none;">
                            <p>جميع الحقوق محفوظة &copy; شركة آفاق الخليج {{ date('Y') }}</p>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="current_sales_rep_id" value="{{ $Clients[0]['sales_rep_id'] ?? '' }}">

                <div class="pagination" id="pagination"></div>
                <div id="clientEditModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50 hidden">
                    <div class="bg-white rounded-lg shadow-lg w-full max-w-5xl mx-4 max-h-[90vh] overflow-hidden">
                        <!-- Modal Header -->
                        <div class="modal-header px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-800" id="modalClientName">تعديل بيانات العميل</h3>
                            <button class="text-gray-500 hover:text-gray-700 text-xl" onclick="closeClientEditModal()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="modal-body px-6 py-4 overflow-y-auto max-h-[65vh]">
                            <form id="clientEditForm" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <input type="hidden" id="editClientId" name="client_id">
                                <div class="col-span-2 flex justify-center mb-4">
                                    <img id="editClientLogo" src="" alt="Client Logo" class="max-h-32 max-w-full object-contain border rounded-lg p-2">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">مندوب المبيعات</label>
                                    <input type="text" id="editSalesRep" name="sales_rep_name" readonly
                                           class="w-full px-3 py-2 border rounded-md bg-gray-100">
                                </div>
                                <!-- Company Information -->
                                <div class="col-span-2">
                                    <h4 class="text-md font-medium text-gray-700 mb-3 border-b pb-2">معلومات الشركة</h4>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">اسم الشركة</label>
                                    <input type="text" id="editCompanyName" name="company_name"
                                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">عنوان الشركة</label>
                                    <input type="text" id="editAddress" name="address"
                                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>


                                <!-- Contact Information -->
                                <div class="col-span-2 mt-4">
                                    <h4 class="text-md font-medium text-gray-700 mb-3 border-b pb-2">معلومات الاتصال</h4>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">الشخص المسؤول</label>
                                    <input type="text" id="editContactPerson" name="contact_person"
                                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">المنصب الوظيفي</label>
                                    <input type="text" id="editContactPosition" name="contact_position"
                                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">رقم الجوال</label>
                                    <input type="tel" id="editPhone" name="phone" dir="ltr"
                                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>

                                {{--                            <div>--}}
                                {{--                                <label class="block text-sm font-medium text-gray-700 mb-1">رابط الواتساب</label>--}}
                                {{--                                <input type="url" id="editWhatsappLink" name="whatsapp_link"--}}
                                {{--                                       class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">--}}
                                {{--                            </div>--}}

                                <!-- Status Information -->
                                <div class="col-span-2 mt-4">
                                    <h4 class="text-md font-medium text-gray-700 mb-3 border-b pb-2">حالة العميل</h4>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">حالة الاهتمام</label>
                                    <select id="editInterestStatus" name="interest_status"
                                            class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                                        <option value="interested">مهتم</option>
                                        <option value="not interested">غير مهتم</option>
                                        <option value="neutral">مؤجل</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">الخدمة المهتم بها</label>
                                    <select id="editInterestedService" name="interested_service"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                                        <option value="">اختر الخدمة</option>
                                        @foreach($services as $service)
                                            <option value="{{ $service->id }}">{{ $service->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">عدد مرات التواصل</label>
                                    <input type="number" id="editContactCount" name="contact_count"
                                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">عدد الخدمة المهتم بها</label>
                                    <input type="number" id="editServiceCount" name="interested_service_count"
                                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>

                                <!-- Additional Information -->
                                <div class="col-span-2 mt-4">
                                    <h4 class="text-md font-medium text-gray-700 mb-3 border-b pb-2">معلومات إضافية</h4>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">تاريخ آخر تواصل</label>
                                    <input type="date" id="editLastContactDate" name="last_contact_date"
                                           class="w-full px-3 py-2 border rounded-md focus:ring-2 focus:ring-blue-500">
                                </div>




                            </form>
                        </div>

                        <!-- Modal Footer -->
                        <div class="modal-footer px-6 py-4 border-t bg-gray-50 flex justify-end gap-3">
                            <button type="button" onclick="closeClientEditModal()"
                                    class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition">
                                إلغاء
                            </button>
                            <button type="button" onclick="saveClientEdits()"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                حفظ التغييرات
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            <!-- Edit Modal -->
            <div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-lg shadow-lg w-full max-w-md mx-4">
                    <div class="modal-header px-6 py-4 border-b">
                        <h3 class="text-lg font-semibold" id="modalTitle">تعديل البيانات</h3>
                        <button class="absolute left-4 top-4 text-gray-500 hover:text-gray-700" onclick="closeEditModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body px-6 py-4">
                        <form id="editForm">
                            <input type="hidden" id="editClientId" name="client_id">
                            <input type="hidden" id="editField" name="field">

                            <div class="mb-4">
                                <label id="fieldLabel" class="block text-sm font-medium text-gray-700 mb-2"></label>
                                <div id="inputContainer">
                                    <!-- Input field will be inserted here dynamically -->
                                </div>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                                    إلغاء
                                </button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                    حفظ
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endsection
        @push('scripts')
            <script>
                const isAdmin = @json($isAdmin);

                let ClientsData = [];
                let currentFilteredClients = [];

                document.addEventListener('DOMContentLoaded', function() {
                    // Initialize data
                    ClientsData = @json($Clients);
                    currentFilteredClients = [...ClientsData];

                    console.log("Clients Data:", ClientsData);

                    // Render initial table
                    renderTable();

                    // Setup event listeners
                    document.getElementById('searchInput').addEventListener('input', function(e) {
                        const searchTerm = e.target.value.toLowerCase();
                        const filteredData = currentFilteredClients.filter(client => {
                            return (
                                (client.company_name && client.company_name.toLowerCase().includes(searchTerm)) ||
                                (client.address && client.address.toLowerCase().includes(searchTerm)) ||
                                (client.phone && client.phone.toLowerCase().includes(searchTerm)) ||
                                (client.interest_status && client.interest_status.toLowerCase().includes(searchTerm)) ||
                                (client.response_status && client.response_status.toLowerCase().includes(searchTerm))
                            );
                        });
                        renderTable(filteredData);
                    });
                    // Export dropdown functionality
                    const exportBtn = document.getElementById('exportBtn');
                    const dropdown = document.getElementById('exportDropdown');

                    exportBtn.addEventListener('click', function(e) {
                        e.stopPropagation();
                        this.closest('.dropdown').classList.toggle('active');
                    });

                    // Close dropdown when clicking outside
                    document.addEventListener('click', function() {
                        document.querySelectorAll('.dropdown').forEach(drop => {
                            drop.classList.remove('active');
                        });
                    });

                    // Handle export option clicks
                    document.querySelectorAll('.dropdown-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const exportType = this.getAttribute('data-type');
                            const selectedColumns = getSelectedColumns();

                            if (exportType === 'csv') {
                                exportClients('csv', selectedColumns);
                            } else if (exportType === 'pdf') {
                                exportToPDF(selectedColumns);
                            }else{
                                exportClients();
                            }

                            // Close dropdown after selection
                            this.closest('.dropdown').classList.remove('active');
                        });
                    });

                    // Prevent dropdown from closing when clicking inside it
                    dropdown.addEventListener('click', function(e) {
                        e.stopPropagation();
                    });
                });
                function toggleFakePlaceholder(input) {
                    const placeholder = document.getElementById('fakePlaceholder');
                    placeholder.style.display = input.value ? 'none' : 'block';
                }

                function resetDateFilter() {
                    const input = document.getElementById('dateFilter');
                    input.value = '';
                    toggleFakePlaceholder(input);
                    // add your reset logic here...
                }

                // Initialize on page load (in case date is pre-filled)
                window.addEventListener('DOMContentLoaded', () => {
                    toggleFakePlaceholder(document.getElementById('dateFilter'));
                });

                function renderTable(data = currentFilteredClients) {
                    const tbody = document.getElementById('tableBody');
                    if (!tbody) {
                        console.error("Table body element not found!");
                        return;
                    }
                    tbody.innerHTML = '';

                    // Debug: Check the value
                    console.log("isAdmin value:", isAdmin);

                    if (!data || data.length === 0) {
                        tbody.innerHTML = `
            <tr>
                <td colspan="14" class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="empty-text">لا توجد بيانات متاحة</div>
                </td>
            </tr>
        `;
                        return;
                    }

                    data.forEach(client => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
            <!-- Company Logo -->
            <td class="px-4 py-2 text-center no-print">
                ${client.company_logo
                            ? `<div class="h-20 w-20 mx-auto border rounded-full p-3 bg-white flex items-center justify-center">
                         <img src="${client.company_logo}" alt="شعار" class="max-h-full max-w-full object-contain" />
                       </div>`
                            : '—'}
            </td>

            <!-- Company Name -->
<td class="px-4 py-2 text-sm font-semibold text-gray-800">
    <div class="flex flex-col items-center">
        <!-- Clickable company name -->
        <span class="cell-value clickable-cell text-center mb-1"
              onclick="redirectToClient(${client.sales_rep_id}, ${client.client_id})">
            ${client.company_name || '—'}
        </span>

        <!-- Sales rep name -->
        <div class="text-xs text-gray-500">
            ${client.sales_rep_name || '—'}
        </div>


    </div>
</td>

<!-- Address Cell -->
<td class="px-4 py-2 text-sm text-gray-600">
    <div class="flex items-center justify-between">
        <span class="cell-value clickable-cell"
              onclick="redirectToClient(${client.sales_rep_id}, ${client.client_id})">
            ${client.address || '—'}
        </span>

    </div>
</td>
            <!-- Contact Person -->
<td class="px-4 py-2 text-sm text-gray-700">
    <div class="flex items-center justify-between">
        <span class="cell-value clickable-cell"
              onclick="redirectToClient(${client.sales_rep_id}, ${client.client_id})">
            ${client.contact_person || '—'}
        </span>

    </div>
</td>

<!-- Contact Position Cell -->
<td class="px-4 py-2 text-sm text-gray-700">
    <div class="flex items-center justify-between">
        <span class="cell-value clickable-cell"
              onclick="redirectToClient(${client.sales_rep_id}, ${client.client_id})">
            ${client.contact_position || '—'}
        </span>

    </div>
</td>

<!-- Phone Cell -->
<td class="px-4 py-2 text-sm text-blue-700 font-bold">
    <div class="flex items-center justify-between">
        <span class="cell-value clickable-cell"
              onclick="redirectToClient(${client.sales_rep_id}, ${client.client_id})">
            <span dir="ltr" class="ltr-number">
                ${client.phone ? (client.phone.startsWith('+') ? client.phone : '+' + client.phone) : '—'}
            </span>
        </span>

    </div>
</td>
            <!-- WhatsApp Link -->
            <td class="px-4 py-2 text-sm text-center">
                <span class="cell-value">
                    ${client.whatsapp_link
                            ? `<a href="${client.whatsapp_link}" class="text-green-600 hover:underline" target="_blank">
                              <i class="fab fa-whatsapp"></i> تواصل
                           </a>`
                            : '—'
                        }
                </span>
            </td>

            <!-- Interest Status -->
          <td class="px-4 py-2 text-sm text-center">
    <div class="flex items-center justify-between">
        <span class="cell-value clickable-cell"
              onclick="redirectToClient(${client.sales_rep_id}, ${client.client_id})">
            <span class="inline-block px-2 py-0.5 rounded-full ${getStatusClass(client.interest_status)}">
                ${getStatusText(client.interest_status)}
            </span>
        </span>

    </div>
</td>

            <!-- Interested Service -->
           <td class="px-4 py-2 text-sm font-medium text-blue-700" dir="rtl">
    ${client.interested_service ? client.interested_service : '—'}
</td>

<td class="px-4 py-2 text-sm font-medium text-gray-600" dir="rtl">
    ${client.interested_service_count > 0 ? `
        <span class="inline-flex items-center px-2 py-0.5 rounded bg-green-100 text-blue-800">
            ${client.interested_service_count}
        </span>
    ` : '-'}
</td>




            <!-- Last Contact Date -->
            <td class="px-4 py-2 text-sm text-center ${ client.is_late_customer ? 'text-red-600 font-bold' : 'text-green-600 font-bold' }">
                ${formatDateForDisplay(client.last_contact_date) || '—'}
            </td>

            <!-- Days Left -->
            <td class="px-4 py-2 text-sm text-center ${client.is_late_customer ? 'text-red-600 font-bold' : 'text-green-600 font-bold'}">
                ${client.contact_days_left ?
                            `${client.contact_days_left} ${getArabicDaysWord(client.contact_days_left)}` :
                            '—'
                        }
            </td>

            <!-- Contact Count -->
            <td class="px-4 py-2 text-sm text-center">
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-blue-100 text-blue-800">
                    ${client.contact_count || 0}
                </span>
            </td>

            <!-- Requests Count -->
            <td class="px-4 py-2 text-sm text-center text-gray-400 cursor-pointer"
                onclick="window.location.href='/salesrep/' + ${client.sales_rep_id} + '/MyRequests'">
                <span class="inline-flex items-center px-2 py-0.5 rounded bg-purple-100 text-blue-800">
                    ${client.requests_count || 0}
                </span>
            </td>

            <!-- Message Link -->
            <td class="px-4 py-2 text-sm text-center no-print">
    <a href="/client/${client.client_id}/message" class="text-blue-600 hover:underline mr-2">
        <i class="fas fa-comments"></i>
    </a>
    ${isAdmin ? `
    <button onclick="openClientEditModal(${client.client_id})" class="text-green-600 hover:text-green-800" title="تعديل بيانات العميل">
        <i class="fas fa-edit"></i>
    </button>
    ` : ''}
</td>
        `;
                        tbody.appendChild(row);
                    });
                }

                function addNewClient() {
                    const salesRepId = document.getElementById('current_sales_rep_id').value;

                    if (!salesRepId) {
                        alert("الرجاء تحديد مندوب مبيعات أولاً");
                        return;
                    }

                    window.location.href = "{{ route('sales-reps.clients.create', ['sales_rep' => ':id']) }}".replace(':id', salesRepId);
                }
                function getArabicDaysWord(number) {
                    if (number === 1) {
                        return 'يوم';
                    } else if (number === 2) {
                        return 'يومان';
                    } else if (number > 2 && number <= 10) {
                        return 'أيام';
                    } else {
                        return 'يوماً';
                    }
                }
                function getStatusText(status) {
                    const statusMap = {
                        'interested': 'مهتم',
                        'not interested': 'غير مهتم',
                        'neutral': 'مؤجل'
                    };
                    return statusMap[status] || 'مؤجل';
                }

                function getStatusClass(status) {
                    const classMap = {
                        'interested': 'bg-green-100 text-green-800',
                        'not interested': 'bg-red-100 text-red-800',
                        'neutral': 'bg-gray-100 text-gray-700'
                    };
                    return classMap[status] || 'bg-gray-100 text-gray-700';
                }

                function getArabicStatus(status) {
                    switch (status) {
                        case 'approved':
                            return '<span class="px-2 py-1 rounded-full text-sm font-medium text-green-600 bg-green-100">تمت الموافقة</span>';
                        case 'rejected':
                            return '<span class="px-2 py-1 rounded-full text-sm font-medium text-red-600 bg-red-100">مرفوض</span>';
                        case 'pending':
                            return '<span class="px-2 py-1 rounded-full text-sm font-medium text-orange-600 bg-orange-100">قيد الانتظار</span>';
                        default:
                            return '<span class="text-gray-500">—</span>';
                    }
                }

                function openColumnsModal() {
                    document.getElementById('columnsModal').style.display = 'flex';
                    updateColumnsBadge();
                }

                function closeColumnsModal() {
                    document.getElementById('columnsModal').style.display = 'none';
                }

                function filterColumns() {
                    const searchTerm = document.getElementById('columnsSearch').value.toLowerCase();
                    const columnItems = document.querySelectorAll('.column-item');

                    columnItems.forEach(item => {
                        const columnName = item.querySelector('.column-name').textContent.toLowerCase();
                        if (columnName.includes(searchTerm)) {
                            item.style.display = 'block';
                        } else {
                            item.style.display = 'none';
                        }
                    });
                }

                function toggleSelectAll() {
                    const checkboxes = document.querySelectorAll('.column-checkbox input');
                    const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);

                    checkboxes.forEach(checkbox => {
                        checkbox.checked = !allChecked;
                        // Trigger change event to update UI
                        const event = new Event('change');
                        checkbox.dispatchEvent(event);
                    });

                    updateColumnsBadge();
                }

                function resetSelection() {
                    const checkboxes = document.querySelectorAll('.column-checkbox input');
                    checkboxes.forEach(checkbox => {
                        checkbox.checked = true;
                        // Trigger change event to update UI
                        const event = new Event('change');
                        checkbox.dispatchEvent(event);
                    });

                    updateColumnsBadge();
                }

                function applyColumnSelection() {
                    const selectedColumns = [];
                    const checkboxes = document.querySelectorAll('.column-checkbox input:checked');

                    checkboxes.forEach(checkbox => {
                        selectedColumns.push(checkbox.value);
                    });

                    // Update table columns visibility based on selection
                    updateTableColumns(selectedColumns);
                    updateColumnsBadge();
                    closeColumnsModal();
                }

                function updateTableColumns(selectedColumns) {
                    const columnVisibility = {};
                    selectedColumns.forEach(col => columnVisibility[col] = true);

                    // Get all table headers and their corresponding indexes
                    const headers = document.querySelectorAll('.data-table thead th');
                    const columnKeys = Array.from(headers).map(header => getColumnKey(header.textContent.trim()));

                    // Show/hide columns based on selection
                    columnKeys.forEach((key, index) => {
                        const shouldShow = columnVisibility[key] || index === 0 || index === headers.length - 1;
                        headers[index].style.display = shouldShow ? '' : 'none';

                        document.querySelectorAll('.data-table tbody tr').forEach(row => {
                            if (row.cells[index]) {
                                row.cells[index].style.display = shouldShow ? '' : 'none';
                            }
                        });
                    });
                }
                function redirectToClient(salesRepId, clientId){
                    window.location.href =`/sales-reps/${salesRepId}/clients/${clientId}`;
                }

                function getColumnKey(columnName) {
                    const columnMap = {
                        'شعار شركة العميل': 'client_logo',
                        'الشركة': 'company_name',
                        'مقر الشركة': 'address',
                        'الشخص المسؤول': 'contact_person',
                        'المنصب الوظيفي': 'contact_position',
                        'رقم الجوال': 'phone',
                        'الخدمة المهتم بها': 'interested_service',
                        'واتس اب مباشر': 'whatsapp_link',
                        'حالة الاهتمام': 'interest_status',
                        'آخر تواصل': 'last_contact_date',
                        'آخر تواصل منذ': 'contact_days_left',
                        'طلبات العميل': 'requests_count',
                        'عدد مرات التواصل': 'contact_count',
                    };

                    return columnMap[columnName] || columnName.toLowerCase().replace(/\s+/g, '_');
                }

                function updateColumnsBadge() {
                    const checkedCount = document.querySelectorAll('.column-checkbox input:checked').length;
                    document.getElementById('columnsBadge').textContent = checkedCount;
                }

                function getSelectedColumns() {
                    const checkboxes = document.querySelectorAll('.column-checkbox input:checked');
                    return Array.from(checkboxes).map(checkbox => checkbox.value);
                }

                function filterByDate() {
                    const fromDate = document.getElementById('fromDate').value;
                    const toDate = document.getElementById('toDate').value;

                    if (!fromDate || !toDate) {
                        alert('الرجاء تحديد تاريخ البداية والنهاية');
                        return;
                    }

                    const filteredClients = ClientsData.filter(client => {
                        const contactDate = client.last_contact_date;

                        // تأكد أن التاريخ موجود
                        if (!contactDate) return false;

                        // المقارنة بنطاق التاريخ
                        return contactDate >= fromDate && contactDate <= toDate;
                    });

                    if (filteredClients.length === 0) {
                        alert('لا توجد نتائج في الفترة المحددة');
                        return;
                    }

                    currentFilteredClients = filteredClients;
                    renderTable(currentFilteredClients);
                }
                function resetDateFilter() {
                    document.getElementById('fromDate').value = '';
                    document.getElementById('toDate').value = '';
                    currentFilteredClients = [...ClientsData];
                    renderTable(currentFilteredClients);
                }

                function applyServiceFilter() {
                    const serviceId = document.getElementById('serviceTypeFilter').value;

                    if (!serviceId || serviceId === "") {
                        currentFilteredClients = [...ClientsData];
                        renderTable(currentFilteredClients);
                        return;
                    }

                    const serviceTypeMap = {
                        @foreach($services as $service)
                        '{{ $service->id }}': '{{ $service->name }}',
                        @endforeach
                    };

                    const serviceName = serviceTypeMap[serviceId];

                    currentFilteredClients = ClientsData.filter(client => {
                        if (!client.service_type) return false;

                        const clientService = client.service_type.trim();
                        const targetService = serviceName.trim();

                        return clientService.localeCompare(targetService, undefined, {
                            sensitivity: 'base',
                            ignorePunctuation: true
                        }) === 0;
                    });

                    if (currentFilteredClients.length === 0) {
                        alert('⚠️ لا يوجد عملاء في هذه الخدمة');
                    }

                    renderTable(currentFilteredClients);
                }
                function applyFilter() {
                    const criteria = document.getElementById('filterSelect').value;
                    const serviceId = document.getElementById('serviceTypeFilter').value;
                    const salesRepName = document.getElementById('salesRepFilter').value;
                    const createdAtDate = document.getElementById('createdAtFilter').value;

                    // Generate service map
                    const serviceTypeMap = {
                        @foreach($services as $service)
                        '{{ $service->id }}': '{{ $service->name }}',
                        @endforeach
                    };

                    currentFilteredClients = [...ClientsData];

                    // Apply sales rep filter if selected
                    if (salesRepName && salesRepName !== "") {
                        currentFilteredClients = currentFilteredClients.filter(client =>
                            client.sales_rep_name === salesRepName
                        );
                    }

                    if (createdAtDate) {
                        currentFilteredClients = currentFilteredClients.filter(client => {
                            if (!client.client_created_at) return false;
                            const clientDate = new Date(client.client_created_at).toISOString().split('T')[0];
                            return clientDate === createdAtDate;
                        });
                    }

                    // Apply status criteria filter if selected
                    if (criteria && criteria !== "") {
                        switch (criteria.toLowerCase()) {
                            case 'neutral':
                                currentFilteredClients = currentFilteredClients.filter(client =>
                                    client.response_status &&
                                    client.response_status.toLowerCase() === 'neutral'
                                );
                                break;

                            case 'interested':
                                currentFilteredClients = currentFilteredClients.filter(client =>
                                    client.interest_status &&
                                    (client.interest_status.toLowerCase() === 'interested' ||
                                        client.interest_status.toLowerCase() === 'intersted')
                                );
                                break;

                            case 'not interested':
                                currentFilteredClients = currentFilteredClients.filter(client =>
                                    client.interest_status &&
                                    (client.interest_status.toLowerCase() === 'not interested' ||
                                        client.interest_status.toLowerCase() === 'not_interested' ||
                                        client.interest_status.toLowerCase() === 'notinterested')
                                );
                                break;
                            case 'late':
                                // Filter for all late customers
                                currentFilteredClients = currentFilteredClients.filter(client =>
                                    client.is_late_customer === true
                                );
                                break;

                            case 'late_interested':
                                // Filter for late AND interested customers
                                currentFilteredClients = currentFilteredClients.filter(client =>
                                    client.is_late_customer === true &&
                                    client.interest_status &&
                                    (client.interest_status.toLowerCase() === 'interested' ||
                                        client.interest_status.toLowerCase() === 'intersted')
                                );
                                break;

                            case 'late_not_interested':
                                currentFilteredClients = currentFilteredClients.filter(client =>
                                    client.is_late_customer === true &&
                                    client.interest_status &&
                                    (client.interest_status.toLowerCase() === 'not interested' ||
                                        client.interest_status.toLowerCase() === 'not_interested' ||
                                        client.interest_status.toLowerCase() === 'notinterested')
                                );
                                break;
                            case 'late_neutral':
                                currentFilteredClients = currentFilteredClients.filter(client =>
                                    client.is_late_customer === true &&
                                    client.interest_status &&
                                    client.interest_status.toLowerCase() === 'neutral'
                                );
                                break;
                        }
                    }

                    // Apply service filter if selected
                    if (serviceId && serviceId !== "") {
                        const serviceName = serviceTypeMap[serviceId];

                        currentFilteredClients = currentFilteredClients.filter(client => {
                            if (!client.interested_service) return false;

                            const clientService = client.interested_service.trim();
                            const targetService = serviceName.trim();

                            return clientService.localeCompare(targetService, undefined, {
                                sensitivity: 'base',
                                ignorePunctuation: true
                            }) === 0;
                        });
                    }

                    if (currentFilteredClients.length === 0) {
                        alert('⚠️ لا يوجد عملاء يطابقون معايير التصفية');
                    }

                    renderTable(currentFilteredClients);
                }
                function isClientLate(client, lateDaysThreshold = 3) {
                    if (!client.last_contact_date) return false;

                    const lastContactDate = new Date(client.last_contact_date);
                    const today = new Date();

                    // Calculate difference in days
                    const diffTime = today - lastContactDate;
                    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

                    return diffDays > lateDaysThreshold;
                }
                function exportClients(selectedColumns = null) {
                    if (!selectedColumns || selectedColumns.length === 0) {
                        selectedColumns = Array.from(document.querySelectorAll('.column-checkbox input[type="checkbox"]'))
                            .filter(checkbox => checkbox.checked)
                            .map(checkbox => checkbox.value);
                    }

                    const columnsMap = {
                        'client_logo': 'شعار شركة العميل',
                        'company_name': ' الشركة',
                        'address': 'مقر الشركة',
                        'contact_person': ' الشخص المسؤول',
                        'contact_position': 'المنصب الوظيفي',
                        'phone': 'رقم الجوال',
                        'الخدمة المهتم بها': 'interested_service',
                        'whatsapp_link': 'واتس اب مباشر',
                        'interest_status': 'حالة الاهتمام',
                        'last_contact_date': ' آخر تواصل',
                        'contact_count': 'عدد مرات التواصل',
                        'contact_days_left':' آخر تواصل منذ',
                        'requests_count':'طلبات العميل',
                    };

                    const headers = selectedColumns
                        .filter(key => key !== 'client_logo')
                        .map(key => columnsMap[key]);

                    const data = currentFilteredClients.map(client => {
                        const row = {};
                        selectedColumns.forEach(key => {
                            let value = (client[key] !== undefined && client[key] !== null) ? client[key] : '';
                            switch (key) {
                                case 'client_logo':
                                    value = client.company_logo || '';
                                    break;
                                case 'company_name':
                                    value = client.company_name || '';
                                    break;
                                case 'address':
                                    value = client.address || '';
                                    break;
                                case 'contact_person':
                                    value = client.contact_person || '';
                                    break;
                                case 'contact_position':
                                    value = client.contact_position || '';
                                    break;
                                case 'phone':
                                    value = client.phone || '';
                                    break;
                                case 'whatsapp_link':
                                    value = client.whatsapp_link || '';
                                    break;
                                case 'interest_status':
                                    value = client.interest_status === 'interested' ? 'مهتم'
                                        : client.interest_status === 'not interested' ? 'غير مهتم'
                                            : 'مؤجل';
                                    break;
                                case 'last_contact_date':
                                    value = formatDateForDisplay(client.last_contact_date);
                                    break;
                                case 'contact_days_left':
                                    value = client.contact_days_left || '';
                                    break;
                                case 'contact_count':
                                    value = client.contact_count ?? 0;
                                    break;
                                case 'requests_count':
                                    value = client.requests_count ?? 0;
                                    break;
                                default:
                                    value = '';
                            }
                            row[key] = value;
                        });
                        return row;
                    });

                    const wsData = [headers, ...data.map(row => selectedColumns
                        .filter(key => key !== 'client_logo')
                        .map(key => row[key]))];
                    const worksheet = XLSX.utils.aoa_to_sheet(wsData);

                    // Auto-fit columns
                    const colWidths = wsData[0].map((_, colIndex) => {
                        const maxLen = wsData.reduce((max, row) => {
                            const cell = row[colIndex] ? String(row[colIndex]) : '';
                            return Math.max(max, cell.length);
                        }, 10);
                        return { wch: maxLen + 2 };
                    });
                    worksheet['!cols'] = colWidths;

                    const workbook = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(workbook, worksheet, "العملاء");

                    XLSX.writeFile(workbook, `عملاء_المبيعات_${new Date().toISOString().slice(0, 10)}.xlsx`);
                }
                function exportToPDF(selectedColumns) {
                    // Clone the table to modify for PDF export
                    const originalTable = document.querySelector('.data-table');
                    const table = originalTable.cloneNode(true);
                    const pdfHeader = document.querySelector('.pdf-header').cloneNode(true);
                    const pdfFooter = document.querySelector('.pdf-footer').cloneNode(true);

                    // Show header and footer
                    pdfHeader.style.display = 'block';
                    pdfFooter.style.display = 'block';
                    pdfFooter.style.padding = '20px';

                    // Create a container for the PDF content
                    const pdfContainer = document.createElement('div');
                    pdfContainer.style.padding = '20px';
                    pdfContainer.appendChild(pdfHeader);
                    pdfContainer.appendChild(table);
                    pdfContainer.appendChild(pdfFooter);

                    // Get all table headers
                    const headers = table.querySelectorAll('thead th');

                    // Hide columns that are not selected
                    headers.forEach((header, index) => {
                        const columnName = header.textContent.trim();
                        const columnKey = getColumnKey(columnName);

                        // Skip first and last columns (logo and chat) which are marked as no-print
                        if (header.classList.contains('no-print')) {
                            header.style.display = 'none';
                            table.querySelectorAll('tbody tr').forEach(row => {
                                if (row.cells[index]) {
                                    row.cells[index].style.display = 'none';
                                }
                            });
                            return;
                        }

                        if (!selectedColumns.includes(columnKey)) {
                            header.style.display = 'none';
                            table.querySelectorAll('tbody tr').forEach(row => {
                                if (row.cells[index]) {
                                    row.cells[index].style.display = 'none';
                                }
                            });
                        }
                    });

                    // Options for html2pdf
                    const options = {
                        margin: 10,
                        filename: `تقرير_عملاء_الشركة_${new Date().toISOString().slice(0,10)}.pdf`,
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: {
                            scale: 2,
                            scrollX: 0,
                            scrollY: 0,
                            windowWidth: document.documentElement.offsetWidth
                        },
                        jsPDF: {
                            unit: 'mm',
                            format: [594, 420],
                            orientation: 'landscape',
                            compress: true
                        }
                    };

                    // Generate PDF
                    html2pdf().set(options).from(pdfContainer).save();
                }

                function formatDateForDisplay(date) {
                    if (!date) return "";
                    try {
                        const d = new Date(date);
                        return isNaN(d.getTime()) ? String(date) : d.toLocaleDateString('ar-EG');
                    } catch (e) {
                        console.warn("Date formatting failed for:", date);
                        return String(date);
                    }
                }
                function previewLogoUpload(input) {
                    const logoPreview = document.getElementById('logoPreview');


                    if (input.files && input.files[0]) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            logoPreview.src = e.target.result;
                            logoPreview.style.display = 'block';

                        }

                        reader.readAsDataURL(input.files[0]);
                    }
                }

                function startEditing(cell) {
                    // If already in edit mode, do nothing
                    if (cell.querySelector('.edit-form')) return;

                    const value = cell.querySelector('.cell-value').textContent;
                    const clientId = cell.getAttribute('data-client-id');
                    const field = cell.getAttribute('data-field');

                    // Special handling for whatsapp link
                    let displayValue = value;
                    if (field === 'whatsapp_link' && value !== '—') {
                        const linkElement = cell.querySelector('a');
                        displayValue = linkElement ? linkElement.getAttribute('href') : value;
                    }

                    cell.innerHTML = `
        <div class="edit-form">
            <input type="text" class="edit-input" value="${displayValue}" data-original-value="${displayValue}">
            <div class="edit-actions">
                <button class="edit-btn edit-save" onclick="saveEdit(this, ${clientId}, '${field}')">
                    <i class="fas fa-check"></i>
                </button>
                <button class="edit-btn edit-cancel" onclick="cancelEdit(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;

                    // Focus the input
                    cell.querySelector('.edit-input').focus();
                }

                // Special function for interest status (dropdown instead of text input)
                function startEditingInterestStatus(cell) {
                    // If already in edit mode, do nothing
                    if (cell.querySelector('.edit-form')) return;

                    const clientId = cell.getAttribute('data-client-id');
                    const field = cell.getAttribute('data-field');
                    const currentStatus = cell.querySelector('.cell-value span').textContent.trim();

                    // Map display text to values
                    const statusMap = {
                        'مهتم': 'interested',
                        'غير مهتم': 'not interested',
                        'مؤجل': 'neutral'
                    };

                    const currentValue = statusMap[currentStatus] || 'neutral';

                    cell.innerHTML = `
        <div class="edit-form">
            <select class="edit-input" data-original-value="${currentValue}">
                <option value="interested" ${currentValue === 'interested' ? 'selected' : ''}>مهتم</option>
                <option value="not interested" ${currentValue === 'not interested' ? 'selected' : ''}>غير مهتم</option>
                <option value="neutral" ${currentValue === 'neutral' ? 'selected' : ''}>مؤجل</option>
            </select>
            <div class="edit-actions">
                <button class="edit-btn edit-save" onclick="saveEdit(this, ${clientId}, '${field}')">
                    <i class="fas fa-check"></i>
                </button>
                <button class="edit-btn edit-cancel" onclick="cancelEdit(this)">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    `;
                }

                function saveEdit(button, clientId, field) {
                    const form = button.closest('.edit-form');
                    const input = form.querySelector('.edit-input');
                    let newValue;

                    // Handle different input types
                    if (input.tagName === 'SELECT') {
                        newValue = input.value;
                    } else {
                        newValue = input.value.trim();
                    }

                    const originalValue = input.getAttribute('data-original-value');

                    if (newValue === originalValue) {
                        cancelEdit(button);
                        return;
                    }

                    // Show loading state
                    button.disabled = true;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

                    // Send AJAX request to update the value
                    fetch(`/api/clients/${clientId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            [field]: newValue
                        })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update the cell with the new value
                                const cell = button.closest('.editable-cell');
                                updateCellDisplay(cell, field, newValue);

                                // If phone was updated, also update the WhatsApp link
                                if (field === 'phone') {
                                    updateWhatsAppLink(clientId, newValue);
                                }

                                // Update the data in our local array
                                const clientIndex = ClientsData.findIndex(c => c.client_id === clientId);
                                if (clientIndex !== -1) {
                                    ClientsData[clientIndex][field] = newValue;

                                    // If phone was updated, update WhatsApp link in local data too
                                    if (field === 'phone') {
                                        const cleanPhone = newValue.replace(/\D/g, ''); // Remove non-digit characters
                                        ClientsData[clientIndex].whatsapp_link = `https://wa.me/${cleanPhone}`;
                                    }
                                }

                                // Show success message
                                showNotification('تم تحديث البيانات بنجاح يا قمر', 'success');
                            } else {
                                throw new Error(data.message || 'فشل في التحديث');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('حدث خطأ أثناء التحديث', 'error');
                            cancelEdit(button);
                        });
                }

                // New function to update WhatsApp link display
                function updateWhatsAppLink(clientId, phoneNumber) {
                    // Find the row for this client
                    const rows = document.querySelectorAll('.data-table tbody tr');

                    for (const row of rows) {
                        // Find the phone cell in this row to check if it's the right client
                        const phoneCell = row.querySelector('[data-field="phone"]');
                        if (phoneCell && phoneCell.getAttribute('data-client-id') == clientId) {
                            // Find the WhatsApp link cell (7th cell, index 6)
                            const whatsappCell = row.cells[6];
                            if (whatsappCell) {
                                // Generate new WhatsApp link - clean phone number and add WhatsApp URL
                                const cleanPhone = phoneNumber.replace(/\D/g, ''); // Remove non-digit characters
                                const whatsappLink = `https://wa.me/${cleanPhone}`;

                                // Update the cell content
                                whatsappCell.innerHTML = `
                    <span class="cell-value">
                        <a href="${whatsappLink}" class="text-green-600 hover:underline" target="_blank">
                            <i class="fab fa-whatsapp"></i> تواصل
                        </a>
                    </span>
                `;
                            }
                            break;
                        }
                    }
                }


                function cancelEdit(button) {
                    const cell = button.closest('.editable-cell');
                    const field = cell.getAttribute('data-field');
                    const clientId = cell.getAttribute('data-client-id');

                    // Find the original value from our data
                    const client = ClientsData.find(c => c.client_id == clientId);
                    if (client) {
                        updateCellDisplay(cell, field, client[field]);
                    }
                }

                // Helper function to update cell display based on field type
                function updateCellDisplay(cell, field, value) {
                    switch (field) {
                        case 'company_name':
                            cell.innerHTML = `
                <span class="cell-value">${value || '—'}</span>
                <i class="fas fa-edit edit-icon"></i>
            `;
                            break;

                        case 'phone':
                            const formattedPhone = value ? (value.startsWith('+') ? value : '+' + value) : '—';
                            cell.innerHTML = `
                <span class="cell-value">
                    <span dir="ltr" class="ltr-number">${formattedPhone}</span>
                </span>
                <i class="fas fa-edit edit-icon"></i>
            `;
                            break;



                        case 'interest_status':
                            const statusText = {
                                'interested': 'مهتم',
                                'not interested': 'غير مهتم',
                                'neutral': 'مؤجل'
                            }[value] || 'مؤجل';

                            const statusClass = {
                                'interested': 'bg-green-100 text-green-800',
                                'not interested': 'bg-red-100 text-red-800',
                                'neutral': 'bg-gray-100 text-gray-700'
                            }[value] || 'bg-gray-100 text-gray-700';

                            cell.innerHTML = `
                <span class="cell-value">
                    <span class="inline-block px-2 py-0.5 rounded-full ${statusClass}">
                        ${statusText}
                    </span>
                </span>
                <i class="fas fa-edit edit-icon"></i>
            `;
                            break;

                        default:
                            cell.innerHTML = `
                <span class="cell-value">${value || '—'}</span>
                <i class="fas fa-edit edit-icon"></i>
            `;
                    }
                }

                function showNotification(message, type) {
                    // Create notification element
                    const notification = document.createElement('div');
                    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 6px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    `;

                    notification.style.backgroundColor = type === 'success' ? '#10b981' : '#ef4444';
                    notification.textContent = message;

                    document.body.appendChild(notification);

                    // Remove notification after 3 seconds
                    setTimeout(() => {
                        notification.style.opacity = '0';
                        notification.style.transition = 'opacity 0.5s';
                        setTimeout(() => notification.remove(), 500);
                    }, 3000);
                }
                function filterByCreatedDate() {
                    const createdAtDate = document.getElementById('createdAtFilter').value;

                    if (!createdAtDate) {
                        alert('الرجاء تحديد تاريخ الإنشاء');
                        return;
                    }

                    const filteredClients = ClientsData.filter(client => {
                        if (!client.client_created_at) return false;

                        // Convert both dates to YYYY-MM-DD format for comparison
                        const clientDate = new Date(client.client_created_at).toISOString().split('T')[0];
                        return clientDate === createdAtDate;
                    });

                    if (filteredClients.length === 0) {
                        alert('لا توجد عملاء تم إنشاؤهم في هذا التاريخ');
                        return;
                    }

                    currentFilteredClients = filteredClients;
                    renderTable(currentFilteredClients);
                }

                function resetCreatedDateFilter() {
                    document.getElementById('createdAtFilter').value = '';
                    hideCreatedAtCount();
                    currentFilteredClients = [...ClientsData];
                    renderTable(currentFilteredClients);
                }

                function updateCreatedAtCount(dateStr) {
                    const count = ClientsData.filter(client => {
                        if (!client.client_created_at) return false;
                        const clientDate = new Date(client.client_created_at).toISOString().split('T')[0];
                        return clientDate === dateStr;
                    }).length;

                    const countBadge = document.getElementById('createdAtCount');
                    countBadge.textContent = count;
                    countBadge.style.display = count > 0 ? 'block' : 'none';
                }

                function hideCreatedAtCount() {
                    document.getElementById('createdAtCount').style.display = 'none';
                }
                function getClientUrl(salesRepId, clientId) {

                    return `/sales-reps/${salesRepId}/clients/${clientId}`;
                }
                function openEditModal(clientId, field, currentValue) {
                    const modal = document.getElementById('editModal');
                    const fieldLabel = document.getElementById('fieldLabel');
                    const inputContainer = document.getElementById('inputContainer');
                    const editClientId = document.getElementById('editClientId');
                    const editField = document.getElementById('editField');

                    // Set modal title and field label based on field type
                    const fieldLabels = {
                        'company_name': 'اسم الشركة',
                        'address': 'عنوان الشركة',
                        'contact_person': 'الشخص المسؤول',
                        'contact_position': 'المنصب الوظيفي',
                        'phone': 'رقم الجوال',
                        'interest_status': 'حالة الاهتمام'
                    };

                    document.getElementById('modalTitle').textContent = `تعديل ${fieldLabels[field]}`;
                    fieldLabel.textContent = fieldLabels[field];
                    editClientId.value = clientId;
                    editField.value = field;

                    // Create appropriate input based on field type
                    if (field === 'interest_status') {
                        inputContainer.innerHTML = `
            <select name="value" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="interested" ${currentValue === 'interested' ? 'selected' : ''}>مهتم</option>
                <option value="not interested" ${currentValue === 'not interested' ? 'selected' : ''}>غير مهتم</option>
                <option value="neutral" ${currentValue === 'neutral' ? 'selected' : ''}>مؤجل</option>
            </select>
        `;
                    } else if (field === 'phone') {
                        inputContainer.innerHTML = `
            <input type="tel" name="value" value="${currentValue}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                   dir="ltr">
        `;
                    } else {
                        inputContainer.innerHTML = `
            <input type="text" name="value" value="${currentValue}"
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
        `;
                    }

                    modal.classList.remove('hidden');
                }

                // Close modal
                function closeEditModal() {
                    document.getElementById('editModal').classList.add('hidden');
                }

                // Handle form submission
                document.getElementById('editForm').addEventListener('submit', function(e) {
                    e.preventDefault();

                    const formData = new FormData(this);
                    const clientId = formData.get('client_id');
                    const field = formData.get('field');
                    const value = formData.get('value');

                    // Show loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'جاري الحفظ...';

                    // Send AJAX request
                    fetch(`/api/clients/${clientId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ [field]: value })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Update local data
                                const clientIndex = ClientsData.findIndex(c => c.client_id == clientId);
                                if (clientIndex !== -1) {
                                    ClientsData[clientIndex][field] = value;
                                }

                                // Update current filtered data if needed
                                const filteredIndex = currentFilteredClients.findIndex(c => c.client_id == clientId);
                                if (filteredIndex !== -1) {
                                    currentFilteredClients[filteredIndex][field] = value;
                                }

                                // Re-render table
                                renderTable();

                                // Show success message
                                showNotification('تم تحديث البيانات بنجاح', 'success');
                                closeEditModal();
                            } else {
                                throw new Error(data.message || 'فشل في التحديث');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('حدث خطأ أثناء التحديث', 'error');
                        })
                        .finally(() => {
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = 'حفظ';
                        });
                });

                // Close modal when clicking outside
                document.getElementById('editModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeEditModal();
                    }
                });
            </script>
            <script>
                flatpickr("#fromDate", {
                    locale: "ar",
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    defaultDate: null,
                });

                flatpickr("#toDate", {
                    locale: "ar",
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    defaultDate: null,
                });

                flatpickr("#createdAtFilter", {
                    locale: "ar",
                    dateFormat: "Y-m-d",
                    allowInput: true,
                    defaultDate: null,
                    onChange: function(selectedDates, dateStr) {
                        if (dateStr) {
                            updateCreatedAtCount(dateStr);
                        } else {
                            hideCreatedAtCount();
                        }
                    }
                });
            </script>


            <script>
                // Function to open the client edit modal
                function openClientEditModal(clientId) {
                    // Find the client data
                    const client = ClientsData.find(c => c.client_id == clientId);

                    if (!client) {
                        showNotification('لم يتم العثور على بيانات العميل', 'error');
                        return;
                    }

                    // Populate the form fields
                    document.getElementById('editClientId').value = client.client_id;
                    document.getElementById('editCompanyName').value = client.company_name || '';
                    document.getElementById('editClientLogo').src = client.company_logo || '/path/to/placeholder/image.jpg';
                    document.getElementById('editAddress').value = client.address || '';
                    document.getElementById('editContactPerson').value = client.contact_person || '';
                    document.getElementById('editContactPosition').value = client.contact_position || '';
                    document.getElementById('editPhone').value = client.phone || '';
                    // document.getElementById('editWhatsappLink').value = client.whatsapp_link || '';
                    document.getElementById('editInterestStatus').value = client.interest_status || 'neutral';
                    document.getElementById('editInterestedService').value = client.interested_service || '';
                    document.getElementById('editContactCount').value = client.contact_count || 0;
                    document.getElementById('editServiceCount').value = client.interested_service_count || 0;
                    document.getElementById('editLastContactDate').value = client.last_contact_date || '';
                    document.getElementById('editSalesRep').value = client.sales_rep_name || '';

                    // Update modal title with client name
                    document.getElementById('modalClientName').textContent = `تعديل بيانات ${client.company_name || 'العميل'}`;

                    // Show the modal - FIXED: Remove the 'hidden' class
                    const modal = document.getElementById('clientEditModal');
                    modal.classList.remove('hidden');
                    modal.style.display = 'flex';
                }

                // Function to close the modal - FIXED
                function closeClientEditModal() {
                    const modal = document.getElementById('clientEditModal');
                    modal.classList.add('hidden');
                    modal.style.display = 'none';
                }

                // Function to save client edits
                function saveClientEdits() {
                    const formData = new FormData(document.getElementById('clientEditForm'));
                    const clientId = formData.get('client_id');

                    // Convert form data to JSON object
                    const data = {};
                    for (let [key, value] of formData.entries()) {
                        if (key !== 'client_id') {
                            data[key] = value;
                        }
                    }

                    // Show loading state
                    const saveBtn = document.querySelector('#clientEditModal .modal-footer button:last-child');
                    const originalText = saveBtn.textContent;
                    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
                    saveBtn.disabled = true;

                    // Send AJAX request
                    fetch(`/api/clients/${clientId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                // Update local data
                                const clientIndex = ClientsData.findIndex(c => c.client_id == clientId);
                                if (clientIndex !== -1) {
                                    Object.assign(ClientsData[clientIndex], data);
                                }

                                // Update current filtered data
                                const filteredIndex = currentFilteredClients.findIndex(c => c.client_id == clientId);
                                if (filteredIndex !== -1) {
                                    Object.assign(currentFilteredClients[filteredIndex], data);
                                }

                                // Re-render table
                                renderTable();

                                // Show success message
                                showNotification('تم تحديث بيانات العميل بنجاح', 'success');
                                closeClientEditModal();
                            } else {
                                throw new Error(result.message || 'فشل في حفظ التغييرات');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('حدث خطأ أثناء حفظ التغييرات', 'error');
                        })
                        .finally(() => {
                            // Restore button state
                            saveBtn.textContent = originalText;
                            saveBtn.disabled = false;
                        });
                }
                function saveAgreementEdits() {
                    const formData = new FormData(document.getElementById('agreementEditForm'));
                    const agreementId = formData.get('agreement_id');

                    const data = {};
                    for (let [key, value] of formData.entries()) {
                        if (key !== 'agreement_id') {
                            data[key] = value;
                        }
                    }

                    // Show loading state
                    const saveBtn = document.querySelector('#agreementEditModal .modal-footer button:last-child');
                    const originalText = saveBtn.textContent;
                    saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...';
                    saveBtn.disabled = true;

                    // Send AJAX request - CORRECTED URL
                    fetch(`/api/agreements/${agreementId}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(data)
                    })
                        .then(response => response.json())
                        .then(result => {
                            if (result.success) {
                                // Update local data
                                const agreementIndex = AgreementsData.findIndex(a => a.agreement_id == agreementId);
                                if (agreementIndex !== -1) {
                                    Object.assign(AgreementsData[agreementIndex], data);
                                }

                                // Update current filtered data
                                const filteredIndex = currentFilteredAgreements.findIndex(a => a.agreement_id == agreementId);
                                if (filteredIndex !== -1) {
                                    Object.assign(currentFilteredAgreements[filteredIndex], data);
                                }

                                // Re-render table
                                renderTable();

                                showNotification('تم تحديث بيانات الاتفاقية بنجاح', 'success');
                                closeAgreementEditModal();
                            } else {
                                throw new Error(result.message || 'فشل في حفظ التغييرات');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            showNotification('حدث خطأ أثناء حفظ التغييرات', 'error');
                        })
                        .finally(() => {
                            saveBtn.textContent = originalText;
                            saveBtn.disabled = false;
                        });
                }
                // Close modal when clicking outside
                document.getElementById('clientEditModal').addEventListener('click', function(e) {
                    if (e.target === this) {
                        closeClientEditModal();
                    }
                });

                // Close modal with Escape key
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && !document.getElementById('clientEditModal').classList.contains('hidden')) {
                        closeClientEditModal();
                    }
                });
            </script>
@endpush
