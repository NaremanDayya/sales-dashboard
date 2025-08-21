<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
class SettingController extends Controller
{
public function index()
    {
        $lateDays = Setting::where('key', 'late_customer_days')->value('value') ?? 3;

        return view('admin.settings.index', compact('lateDays'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'late_customer_days' => 'required|numeric|min:1|max:30',
        ]);

        Setting::updateOrCreate(
            ['key' => 'late_customer_days'],
            ['value' => $request->late_customer_days]
        );

        return back()->with('success', 'تم تحديث عدد أيام التأخير بنجاح.');
    }

    public function updateCommissionThreshold(Request $request)
{
    $request->validate([
        'commission_threshold' => 'required|numeric|min:0|max:100',
    ]);

    Setting::updateOrCreate(
        ['key' => 'commission_threshold'],
        ['value' => $request->commission_threshold]
    );

    return back()->with('success', 'تم تحديث نسبة تحقيق التارجت بنجاح ✅');
}


}
