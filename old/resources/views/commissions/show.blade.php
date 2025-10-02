@extends('layouts.master')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-purple-600 to-purple-800 px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-white">Commission Details</h1>
                <div class="flex space-x-2">
                    <div class="bg-purple-500 px-3 py-1 rounded-full text-xs font-semibold text-white">
                        {{ DateTime::createFromFormat('!m', $commission->month)->format('F') }} {{ $commission->year }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Sales Rep Info -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Sales Representative</h2>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0 h-16 w-16 rounded-full bg-purple-100 flex items-center justify-center">
                            <span class="text-purple-600 text-xl font-bold">{{ substr($commission->salesRep->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-900">{{ $commission->salesRep->name }}</p>
                            <p class="text-sm text-gray-500">{{ $commission->salesRep->email }}</p>
                            <p class="text-sm text-gray-500">{{ $commission->salesRep->phone }}</p>
                        </div>
                    </div>
                </div>

                <!-- Service Info -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Service Details</h2>
                    <div>
                        <p class="text-lg font-medium text-gray-900">{{ $commission->service->name }}</p>
                        <p class="text-sm text-gray-500 mt-1">Target: {{ number_format($commission->target->target_amount, 2) }} SAR</p>
                        <p class="text-sm text-gray-500">Achieved: {{ number_format($commission->total_achieved_amount, 2) }} SAR</p>
                    </div>
                </div>

                <!-- Commission Summary -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Commission Summary</h2>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Rate:</span>
                            <span class="font-medium">{{ $commission->commission_rate }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Achievement:</span>
                            <span class="font-medium">{{ number_format($commission->achieved_percentage, 2) }}%</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Amount:</span>
                            <span class="font-medium text-purple-600">{{ number_format($commission->commission_amount, 2) }} SAR</span>
                        </div>
                        <div class="flex justify-between pt-2 border-t mt-2">
                            <span class="text-gray-600">Period:</span>
                            <span class="font-medium">
                                {{ DateTime::createFromFormat('!m', $commission->month)->format('F') }} {{ $commission->year }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Performance Visualization -->
            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Achievement Progress -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Performance Achievement</h2>
                    <div class="flex items-center justify-center">
                        <div class="relative w-48 h-48">
                            <svg class="w-full h-full" viewBox="0 0 100 100">
                                <!-- Background circle -->
                                <circle cx="50" cy="50" r="45" fill="none" stroke="#e2e8f0" stroke-width="8"/>
                                <!-- Progress circle -->
                                <circle cx="50" cy="50" r="45" fill="none"
                                        stroke="#10b981" stroke-width="8" stroke-linecap="round"
                                        stroke-dasharray="283"
                                        stroke-dashoffset="{{ 283 - (283 * min($commission->achieved_percentage, 100)) / 100 }}"/>
                                <!-- Center text -->
                                <text x="50" y="50" font-family="Arial" font-size="16" text-anchor="middle" dominant-baseline="middle" fill="#4b5563">
                                    {{ number_format($commission->achieved_percentage, 1) }}%
                                </text>
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600">
                            {{ $commission->achieved_percentage >= 100 ? 'Target Exceeded!' :
                               ($commission->achieved_percentage >= 90 ? 'Target Almost Achieved' :
                               ($commission->achieved_percentage >= 50 ? 'Halfway There' : 'Needs Improvement')) }}
                        </p>
                    </div>
                </div>

                <!-- Commission Breakdown -->
                <div class="bg-white p-4 rounded-lg border border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Commission Breakdown</h2>
                    <div class="space-y-3">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Total Sales</span>
                                <span>{{ number_format($commission->total_achieved_amount, 2) }} SAR</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-blue-600 h-2.5 rounded-full" style="width: 100%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Commission Rate</span>
                                <span>{{ $commission->commission_rate }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-purple-600 h-2.5 rounded-full" style="width: {{ $commission->commission_rate }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-600">Your Commission</span>
                                <span class="font-medium">{{ number_format($commission->commission_amount, 2) }} SAR</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-green-600 h-2.5 rounded-full"
                                     style="width: {{ ($commission->commission_amount / $commission->total_achieved_amount) * 100 }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('commissions.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                    Back to Commissions
                </a>
                <button onclick="window.print()"
                        class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    Print Details
                </button>
                <a href="{{ route('commissions.export', $commission) }}"
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                    Export as PDF
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
