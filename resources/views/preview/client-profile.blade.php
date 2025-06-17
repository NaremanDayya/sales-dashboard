<!DOCTYPE html>
<html>
<head>
    <title>Preview: {{ $client->company_name }} Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            body {
                padding: 0;
                margin: 0;
                font-size: 12pt;
            }
            .print-container {
                margin: 0;
                padding: 0;
                box-shadow: none;
            }
        }
        .print-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            background: white;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="no-print py-4 bg-blue-600 text-white">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <h1 class="text-xl font-bold">PDF Preview</h1>
            <div class="space-x-4">
                <button onclick="window.print()" class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-blue-50 transition">
                    Print PDF
                </button>
                <a href="{{ $downloadUrl }}" class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-800 transition">
                    Download PDF
                </a>
                <button onclick="window.close()" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition">
                    Close
                </button>
            </div>
        </div>
    </div>

    <div class="print-container">
        <!-- Include the actual PDF content -->
        @include('pdf.client-profile', ['client' => $client])
    </div>

    <div class="no-print py-8 text-center">
        <p class="text-gray-500">This is a preview. Use the buttons above to print or download.</p>
    </div>
</body>
</html>
