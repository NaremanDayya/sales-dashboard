<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\AgreementEditRequest;
use App\Models\TemporaryPermission;
use App\Models\User;
use App\Notifications\AgreementEditRequestApprovedNotification;
use App\Notifications\AgreementEditRequestRejectedNotification;
use Illuminate\Http\Request;

class AgreementEditRequestController extends Controller
{
    public function edit(Agreement $agreement, AgreementEditRequest $agreement_request)
    {
        $columns = [
            'service_id' => 'الخدمة',
            'signing_date' => 'تاريخ التوقيع',
            'duration_years' => 'مدة الاتفاقية (بالسنوات)',
            'start_date' => 'تاريخ البدء',
            'implementation_date' => 'تاريخ التنفيذ',
            'end_date' => 'تاريخ الانتهاء',
            'termination_type' => 'نوع الإنهاء',
            'notice_months' => 'عدد أشهر الإشعار',
            'notice_status' => 'حالة الإشعار',
            'product_quantity' => 'كمية المنتج',
            'price' => 'السعر',
            'agreement_status' => 'حالة الاتفاقية',
            'notes' => 'ملاحظات',
            'status' => 'الحالة',
        ];


        return view('agreementRequests.admin.edit', compact('agreement', 'agreement_request', 'columns'));
    }

    public function update(Request $request, Agreement $agreement, AgreementEditRequest $agreement_request)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
        ]);

        try {
            \DB::beginTransaction();

            $agreement_request->update([
                'status' => $request->status,
                'response_status' => $request->status,
                'response_date' => now(),
            ]);

            $user = User::where('id', $agreement_request->sales_rep_id)->first();

            if ($agreement_request->status === 'approved') {
                // Handle temporary permission
                $existingPermission = TemporaryPermission::where([
                    'user_id' => $agreement_request->sales_rep_id,
                    'permissible_type' => Agreement::class,
                    'permissible_id' => $agreement_request->agreement_id,
                    'field' => $agreement_request->edited_field,
                ])->first();

                if ($existingPermission) {
                    $existingPermission->update([
                        'expires_at' => now()->addHours(24)
                    ]);
                } else {
                    TemporaryPermission::create([
                        'user_id' => $agreement_request->sales_rep_id,
                        'permissible_type' => Agreement::class,
                        'permissible_id' => $agreement_request->agreement_id,
                        'field' => $agreement_request->edited_field,
                        'expires_at' => now()->addHours(24),
                    ]);
                }

                if ($user) {
                    $user->notify(new AgreementEditRequestApprovedNotification($agreement_request));
                }
            } elseif ($agreement_request->status === 'rejected') {
                if ($user) {
                    $user->notify(new AgreementEditRequestRejectedNotification($agreement_request));
                }
            }

            \DB::commit();

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث حالة طلب الاتفاقية بنجاح',
                    'status' => $request->status
                ]);
            }

            return redirect()
                ->route('salesrep.agreements.show', [
                    'salesrep' => $agreement->sales_rep_id,
                    'agreement' => $agreement->id,
                ])
                ->with('success', 'تمت مراجعة طلب التعديل بنجاح.');

        } catch (\Exception $e) {
            \DB::rollBack();

            // Return JSON response for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'فشل في تحديث حالة طلب الاتفاقية: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'فشل في تحديث حالة طلب الاتفاقية');
        }
    }    public function review(Agreement $agreement, AgreementEditRequest $agreement_request)
    {
        $agreement_request->load(['agreement', 'client', 'salesRep']);
        $editedFieldLabel = $this->getFieldLabel($agreement_request->edited_field);
        return view('agreementRequests.admin.review', compact('agreement_request', 'editedFieldLabel'));
    }
    protected function getFieldLabel($field)
    {
        $labels = [
            'service_id' => 'الخدمة',
            'signing_date' => 'تاريخ التوقيع',
            'duration_years' => 'مدة الاتفاق (بالسنوات)',
            'end_date' => 'تاريخ الانتهاء',
            'termination_type' => 'نوع الإنهاء',
            'notice_months' => 'مدة الإشعار (بالأشهر)',
            'notice_status' => 'حالة الإشعار',
            'product_quantity' => 'كمية المنتج',
            'price' => 'السعر',
            'agreement_status' => 'حالة الاتفاق',
            'implementation_date' => 'تاريخ التنفيذ',
        ];

        return $labels[$field] ?? $field;
    }
}
