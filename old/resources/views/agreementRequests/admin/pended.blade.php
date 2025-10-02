@extends('layouts.master')

@section('content')
<div class="pagetitle">
    <h1>Client Edit Requests</h1>
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
            <li class="breadcrumb-item active">Pended Requests</li>
        </ol>
    </nav>
</div>

<section class="section">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pending Requests</h5>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Client</th>
                                    <th>Request Type</th>
                                    <th>Sales Rep</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pendedRequests as $request)
                                <tr>
                                    <td>{{ $request->id }}</td>
                                    <td>{{ $request->client->name }}</td>
                                    <td>{{ Str::title(str_replace('_', ' ', $request->request_type)) }}</td>
                                    <td>{{ $request->salesRep->name }}</td>
                                    <td>
                                        <span class="badge bg-{{
                                            $request->status === 'pending' ? 'warning' :
                                            ($request->status === 'approved' ? 'success' : 'danger')
                                        }}">
                                            {{ ucfirst($request->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $request->created_at->format('M d, Y h:i A') }}</td>
                                    <td>
                                        <a href="{{ route('admin.client-request.update', [
                                            'client' => $request->client_id,
                                            'client_request' => $request->id
                                        ]) }}"
                                        class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i> Review
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $pendedRequests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
