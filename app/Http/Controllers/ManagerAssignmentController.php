<?php

namespace App\Http\Controllers;

use App\Models\SalesRep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ManagerAssignmentController extends Controller
{
    public function assign(Request $request, SalesRep $salesRep)
    {

        $request->validate([
            'manager_id' => 'required|exists:sales_representatives,id',
        ]);

        $manager = SalesRep::findOrFail($request->manager_id);

        if (!$manager->canBeAssignedAsManagerTo($salesRep)) {
            throw ValidationException::withMessages([
                'manager_id' => 'This assignment would create a circular hierarchy or is invalid.',
            ]);
        }

        DB::transaction(function () use ($salesRep, $manager) {
            $salesRep->update(['manager_id' => $manager->id]);
        });

        return redirect()->back()->with('success', 'Manager assigned successfully.');
    }

    public function remove(SalesRep $salesRep)
    {

        DB::transaction(function () use ($salesRep) {
            $salesRep->update(['manager_id' => null]);
        });

        return redirect()->back()->with('success', 'Manager removed successfully.');
    }

    public function availableManagers(SalesRep $salesRep)
    {

        $availableManagers = SalesRep::where('id', '!=', $salesRep->id)
            ->with('user')
            ->get()
            ->filter(function ($manager) use ($salesRep) {
                return $manager->canBeAssignedAsManagerTo($salesRep);
            });

        return response()->json($availableManagers);
    }
}
