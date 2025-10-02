@extends('layouts.master')
@extends('title','Client Edit Requests')
@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-blue-600 to-blue-800 px-6 py-4">
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold text-white">Edit Request Details</h1>
                <span class="px-3 py-1 rounded-full text-xs font-semibold
                    @if($clientEditRequest->status == 'pending') bg-yellow-100 text-yellow-800
                    @elseif($clientEditRequest->status == 'approved') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucfirst($clientEditRequest->status) }}
                </span>
            </div>
        </div>

        <!-- Main Content -->
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Client Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Client Information</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Client</p>
                            <p class="text-gray-800">{{ $clientEditRequest->client->company_name }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Sales Representative</p>
                            <p class="text-gray-800">{{ $clientEditRequest->salesRep->name }}</p>
                        </div>
                    </div>
                </div>

                <!-- Request Information -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Request Details</h2>
                    <div class="space-y-3">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Request Type</p>
                            <p class="text-gray-800 capitalize">{{ str_replace('_', ' ', $clientEditRequest->request_type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Field to Edit</p>
                            <p class="text-gray-800 capitalize">{{ str_replace('_', ' ', $clientEditRequest->edited_field) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Created At</p>
                            <p class="text-gray-800">{{ $clientEditRequest->created_at->format('M d, Y H:i') }}</p>
                        </div>
                        @if($clientEditRequest->response_date)
                        <div>
                            <p class="text-sm font-medium text-gray-500">Response Date</p>
                            <p class="text-gray-800">{{ $clientEditRequest->response_date->format('M d, Y H:i') }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description & Notes -->
            <div class="mt-6 space-y-6">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Description</h2>
                    <p class="text-gray-700 whitespace-pre-line">{{ $clientEditRequest->description }}</p>
                </div>

                @if($clientEditRequest->notes)
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h2 class="text-lg font-semibold text-gray-800 mb-3 border-b pb-2">Admin Notes</h2>
                    <p class="text-gray-700 whitespace-pre-line">{{ $clientEditRequest->notes }}</p>
                </div>
                @endif
            </div>

            <!-- Actions -->
            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('client-edit-requests.index') }}"
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5 text-gray-500" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                    </svg>
                    Back to Requests
                </a>

                @if($clientEditRequest->status == 'pending' && auth()->user()->can('approve_clientEditRequest'))
                <form action="{{ route('client-edit-requests.approve', $clientEditRequest) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                        Approve Request
                    </button>
                </form>
                @endif

                @if($clientEditRequest->status == 'pending' && auth()->user()->can('reject_clientEditRequest'))
                <form action="{{ route('client-edit-requests.reject', $clientEditRequest) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="-ml-1 mr-2 h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                        Reject Request
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
