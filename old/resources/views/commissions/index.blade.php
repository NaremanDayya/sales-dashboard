@extends('layouts.master')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-purple-800 px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-white">Commissions Dashboard</h1>
                <div class="flex space-x-2">
                    <div class="bg-purple-500 px-3 py-1 rounded-full text-xs font-semibold text-white">
                        {{ now()->format('F Y') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-gray-50 px-6 py-4 border-b">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                    <select id="month" name="month" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 p-2 border">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ request('month') == $i ? 'selected' : '' }}>
                                {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                            </option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                    <select id="year" name="year" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 p-2 border">
                        @for($i = now()->year; $i >= now()->year - 5; $i--)
                            <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label for="sales_rep" class="block text-sm font-medium text-gray-700 mb-1">Sales Rep</label>
                    <select id="sales_rep" name="sales_rep" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-purple-500 focus:ring-purple-500 p-2 border">
                        <option value="">All</option>
                        @foreach($salesReps as $rep)
                            <option value="{{ $rep->id }}" {{ request('sales_rep') == $rep->id ? 'selected' : '' }}>
                                {{ $rep->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Stats Summary -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 p-6 border-b">
            <div class="bg-purple-50 p-4 rounded-lg">
                <p class="text-sm font-medium text-purple-600">Total Commissions</p>
                <p class="text-2xl font-semibold text-purple-900">{{ number_format($totalCommissions, 2) }} SAR</p>
            </div>
            <div class="bg-green-50 p-4 rounded-lg">
                <p class="text-sm font-medium text-green-600">Highest Earner</p>
                <p class="text-xl font-semibold text-green-900">{{ $topPerformer->salesRep->name ?? 'N/A' }}</p>
                <p class="text-sm text-green-700">{{ number_format($topPerformer->commission_amount ?? 0, 2) }} SAR</p>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg">
                <p class="text-sm font-medium text-blue-600">Top Service</p>
                <p class="text-xl font-semibold text-blue-900">{{ $topService->service->name ?? 'N/A' }}</p>
                <p class="text-sm text-blue-700">{{ number_format($topService->total_achieved_amount ?? 0, 2) }} SAR</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-lg">
                <p class="text-sm font-medium text-yellow-600">Avg. Achievement</p>
                <p class="text-2xl font-semibold text-yellow-900">{{ number_format($averageAchievement, 2) }}%</p>
            </div>
        </div>

        <!-- Commissions Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Sales Rep
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Service
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Period
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Achievement
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Commission
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($commissions as $commission)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10 rounded-full bg-purple-100 flex items-center justify-center">
                                    <span class="text-purple-600 font-medium">{{ substr($commission->salesRep->name, 0, 1) }}</span>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $commission->salesRep->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $commission->salesRep->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $commission->service->name }}</div>
                            <div class="text-sm text-gray-500">Target: {{ number_format($commission->target->target_amount, 2) }} SAR</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ DateTime::createFromFormat('!m', $commission->month)->format('F') }} {{ $commission->year }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-20 mr-2">
                                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500 rounded-full"
                                             style="width: {{ min($commission->achieved_percentage, 100) }}%"></div>
                                    </div>
                                </div>
                                <div class="text-sm font-medium text-gray-900">
                                    {{ number_format($commission->achieved_percentage, 2) }}%
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-purple-600">
                                {{ number_format($commission->commission_amount, 2) }} SAR
                            </div>
                            <div class="text-xs text-gray-500">
                                Rate: {{ $commission->commission_rate }}%
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a href="{{ route('commissions.show', $commission) }}" class="text-purple-600 hover:text-purple-900 mr-3">
                                View
                            </a>
                            <a href="#" class="text-green-600 hover:text-green-900" onclick="event.preventDefault(); document.getElementById('export-form-{{ $commission->id }}').submit();">
                                Export
                            </a>
                            <form id="export-form-{{ $commission->id }}" action="{{ route('commissions.export', $commission) }}" method="POST" class="hidden">
                                @csrf
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 border-t">
            {{ $commissions->links() }}
        </div>
    </div>
</div>
@endsection
