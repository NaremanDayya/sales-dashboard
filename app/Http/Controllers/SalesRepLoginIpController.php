<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\SalesRep;
use App\Models\SalesRepLoginIp;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SalesRepLoginIpController extends Controller
{
    public function index(Request $request)
    {
        $query = SalesRep::with(['user', 'loginIps']);

        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        $salesReps = $query->get();

        return view('salesRep.loginIps', compact('salesReps'));
    }

public function block($id)
{
    $salesRepLoginIp = SalesRepLoginIp::findOrFail($id);

    $salesRepLoginIp->update([
        'is_allowed' => false,
        'blocked_at' => now(),
    ]);

    return back()->with('success', 'تم  حظر IP الجهاز بنجاح.');
}

public function unblock($id)
{
    $salesRepLoginIp = SalesRepLoginIp::findOrFail($id);

    $salesRepLoginIp->update([
        'is_allowed' => true,
        'blocked_at' => null,
    ]);

    return back()->with('success', 'تم إلغاء حظر IP الجهاز بنجاح.');
}
    public function addTemporaryIp(Request $request, Salesrep $salesRep)
    {
        $request->validate([
            'ip_address' => 'required|ip',
	                'allowed_until' => 'required|date|after:now',

        ]);

        // إنشاء IP مؤقت جديد
        $salesRep->loginIps()->create([
            'ip_address' => $request->ip_address,
            'is_allowed' => true,
            'is_temporary' => true,
		            'allowed_until' => Carbon::parse($request->allowed_until),

        ]);

        return back()->with('success', 'تم إضافة IP مؤقت بنجاح');
    }
    public function destroy(SalesRepLoginIp $ip)
    {
        /*
        if (!$ip->is_temporary && $ip->is_allowed) {
            $countAllowed = SalesRepLoginIp::where('sales_rep_id', $ip->sales_rep_id)
                ->where('is_allowed', true)
                ->count();

            if ($countAllowed <= 1) {
                return back()->with('error', 'لا يمكن حذف الـ IP المسموح الوحيد لهذا المندوب.');
            }
        }
        */

        $ip->delete(); // لو حابب سجل أثري استخدم SoftDeletes (موضح بالأسفل)
        return back()->with('success', 'تم حذف IP الجهاز بنجاح.');
    }
    public function allow(SalesRepLoginIp $ip, Request $request)
    {
        $ip->update([
            'is_allowed'    => true,
            'is_blocked'    => false,
            'allowed_until' => $request->allowed_until,
            'is_temporary'  => $request->allowed_until ? 1 : 0,
            'blocked_at'    => null,
        ]);

        return back()->with('success', 'تم منح صلاحية لهذا IP بنجاح.');
    }
}

