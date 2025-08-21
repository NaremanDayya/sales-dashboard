<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
  $services = Service::withCount([
        'agreements',
        'agreements as active_agreements_count' => function ($query) {
            $query->where('agreement_status', 'active');
        },
        'agreements as inactive_agreements_count' => function ($query) {
            $query->where('agreement_status', 'expired'); 
        },
    ])->paginate(10);

        return view('services.index', compact('services'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0',
            'is_flat_price' => 'required|boolean',
	    'commission_rate' => 'required|numeric|min:0.01',
        ]);

        Service::create($validated);
        return redirect()->route('services.index')->with('success', 'Service created successfully.');
    }

    public function show(Service $service)
    {
        return view('services.show', compact('service'));
    }

    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'target_amount' => 'required|numeric|min:0',
            'is_flat_price' => 'required|boolean',
            'commission_rate' => 'required|numeric|min:0.01',

		]);

        $service->update($validated);
        return redirect()->route('services.index')->with('success', 'Service updated successfully.');
    }

    public function destroy(Service $service)
    {
        $service->delete();
        return redirect()->route('services.index')->with('success', 'Service deleted successfully.');
    }
}
