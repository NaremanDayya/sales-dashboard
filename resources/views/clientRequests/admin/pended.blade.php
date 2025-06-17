@extends('layouts.master')

@section('content')
<div class="pagetitle">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1>Client Edit Requests</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Pending Requests</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="card-title m-0">Pending Requests</h5>
                        <div class="d-flex">
                            <input type="text" class="form-control me-2" placeholder="Search..." id="searchInput" style="width: 200px;">
                        </div>
                    </div>

                    @if($pendedRequests->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-nowrap">ID</th>
                                    <th>Client</th>
                                    <th>Request Type</th>
                                    <th>Sales Rep</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendedRequests as $request)
                                <tr>
                                    <td class="fw-semibold">#{{ $request->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title bg-light rounded">
                                                    <i class="bi bi-building text-primary"></i>
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $request->client->name }}</h6>
                                                <small class="text-muted">{{ $request->client->company_name ?? 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-nowrap">
                                        <span class="badge bg-info bg-opacity-10 text-info">
                                            {{ Str::title(str_replace('_', ' ', $request->request_type)) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->salesRep->name }}</td>
                                    <td>
                                        <span class="badge rounded-pill bg-{{
                                            $request->status === 'pending' ? 'warning' :
                                            ($request->status === 'approved' ? 'success' : 'danger')
                                        }} py-1 px-3">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap">
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ $request->created_at->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $request->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-end gap-2">
                                            <a href="{{ route('admin.client-request.edit', [
                                                'client' => $request->client_id,
                                                'client_request' => $request->id
                                            ]) }}"
                                            class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Edit Status">
                                                <i class="bi bi-pencil"></i>
                                            </a>

                                            <a href="{{ route('admin.client-request.update', [
                                                'client' => $request->client_id,
                                                'client_request' => $request->id
                                            ]) }}"
                                            class="btn btn-sm btn-primary rounded-pill px-3"
                                            data-bs-toggle="tooltip" data-bs-placement="top" title="Review Request">
                                                <i class="bi bi-eye"></i> Review
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 d-flex justify-content-between align-items-center">
                        <div class="text-muted">
                            Showing {{ $pendedRequests->firstItem() }} to {{ $pendedRequests->lastItem() }} of {{ $pendedRequests->total() }} entries
                        </div>
                        <div>
                            {{ $pendedRequests->links() }}
                        </div>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-inbox display-4 text-muted"></i>
                        </div>
                        <h4 class="text-muted">No Pending Requests</h4>
                        <p class="text-muted">There are currently no pending edit requests.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
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
            table = document.querySelector(".table");
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

@section('styles')
<style>
    .card {
        border: none;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05);
        border-radius: 0.5rem;
    }

    .card-title {
        font-weight: 600;
        color: #2c3e50;
    }

    .table th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        color: #6c757d;
        border-bottom-width: 1px;
    }

    .avatar-sm {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .badge {
        font-weight: 500;
    }

    .btn-outline-primary {
        border-width: 2px;
    }

    .rounded-pill {
        border-radius: 50rem !important;
    }
</style>
@endsection
