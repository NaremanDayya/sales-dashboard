@extends('layouts.master')

@section('content')
<x-page-header title="Pending Requests" subtitle="طلبات تعديل بيانات العملاء المعلَّقة">
    <x-slot name="actions">
        <div class="relative">
            <div class="absolute inset-y-0 start-0 flex items-center ps-3 pointer-events-none text-gray-400">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z"/></svg>
            </div>
            <input type="text" class="ps-9 pe-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" placeholder="Search..." id="searchInput" style="width: 220px;">
        </div>
    </x-slot>
</x-page-header>

<div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
    @if($pendedRequests->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200" id="pendedRequestsTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">ID</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">Client</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">Request Type</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">Sales Rep</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-start text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-3 text-end text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @foreach($pendedRequests as $request)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-4 py-3 whitespace-nowrap text-sm font-semibold text-gray-700">#{{ $request->id }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-indigo-600">
                                    <i class="bi bi-building"></i>
                                </span>
                                <div>
                                    <h6 class="text-sm font-semibold text-gray-900">{{ $request->client->name }}</h6>
                                    <small class="text-xs text-gray-400">{{ $request->client->company_name ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <x-badge color="sky">{{ Str::title(str_replace('_', ' ', $request->request_type)) }}</x-badge>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">{{ $request->salesRep->name }}</td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            @php
                                $badgeColor = match($request->status) { 'pending' => 'amber', 'approved' => 'emerald', default => 'rose' };
                            @endphp
                            <x-badge :color="$badgeColor">{{ ucfirst($request->status) }}</x-badge>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-600">
                            <i class="bi bi-calendar text-gray-400 me-1"></i>
                            {{ $request->created_at->format('M d, Y') }}
                            <br>
                            <small class="text-xs text-gray-400">{{ $request->created_at->format('h:i A') }}</small>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-end">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('admin.client-request.edit', [
                                    'client' => $request->client_id,
                                    'client_request' => $request->id
                                ]) }}"
                                class="inline-flex items-center justify-center h-8 w-8 rounded-full border border-gray-300 text-gray-500 hover:bg-gray-50 transition-colors"
                                title="Edit Status">
                                    <i class="bi bi-pencil text-xs"></i>
                                </a>

                                <a href="{{ route('admin.client-request.update', [
                                    'client' => $request->client_id,
                                    'client_request' => $request->id
                                ]) }}"
                                class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full bg-indigo-600 text-white text-xs font-semibold hover:bg-indigo-700 transition-colors"
                                title="Review Request">
                                    <i class="bi bi-eye"></i> Review
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between">
            <div class="text-xs text-gray-400">
                Showing {{ $pendedRequests->firstItem() }} to {{ $pendedRequests->lastItem() }} of {{ $pendedRequests->total() }} entries
            </div>
            <div>
                {{ $pendedRequests->links() }}
            </div>
        </div>
    @else
        <x-empty-state title="No Pending Requests" description="There are currently no pending edit requests." />
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Enable tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("pendedRequestsTable");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                let found = false;
                const tds = tr[i].getElementsByTagName("td");

                for (let j = 0; j < tds.length - 1; j++) { // Skip actions column
                    if (tds[j]) {
                        txtValue = tds[j].textContent || tds[j].innerText;
                        if (txtValue.toUpperCase().indexOf(filter) > -1) {
                            found = true;
                            break;
                        }
                    }
                }

                if (found) {
                    tr[i].style.display = "";
                } else if (i > 0) { // Skip header row
                    tr[i].style.display = "none";
                }
            }
        });
    });
</script>
@endsection
