<?php

namespace App\Http\Controllers\Api;

use App\Models\Bank;
use Illuminate\Http\Request;
use App\Http\Resources\BankResource;
use App\Http\Resources\EMICalculationResource;

class EMICalculatorController extends BaseController
{
    public function getBanks()
    {
        $banks = Bank::with(['tenures'])->get();
        return BankResource::collection($banks);
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'bank_id' => 'required|exists:banks,id',
            'product_price' => 'required|numeric|min:0',
            'down_payment' => 'required|numeric|min:0',
            'tenure_months' => 'required|integer|min:1',
        ]);

        $bank = Bank::with(['tenures', 'processingFeeRule'])->findOrFail($request->bank_id);

        $financed_amount = $request->product_price - $request->down_payment;

        if ($financed_amount <= 0) {
            return response()->json(['error' => 'Down payment must be less than product price.'], 422);
        }

        $tenure = $bank->tenures->firstWhere('months', $request->tenure_months);
        if (!$tenure) {
            return response()->json(['error' => 'Selected tenure is not available for this bank.'], 422);
        }
        if ($request->product_price < $bank->min_emi_price) {
            return response()->json([
                'error' => 'EMI not available for this product under ' . $bank->name . '. Minimum price: Rs. ' . number_format($bank->min_emi_price),
            ], 422);
        }

        $percent_charge = $financed_amount * ($tenure->service_charge_percent / 100);
        $service_charge = max($percent_charge, $tenure->min_service_charge_amount ?? 0);

        $processing_fee = 0;
        $processingFeeRule = $bank->processingFeeRule;
        if ($processingFeeRule) {
            if ($processingFeeRule->type === 'percentage') {
                $processing_fee = max(
                    ($processingFeeRule->value / 100) * $request->product_price,
                    $processingFeeRule->min_fee ?? 0
                );
            } else {
                $processing_fee = $processingFeeRule->value;
            }
        }

        $emi = round($financed_amount / $tenure->months, 2);

        return new EMICalculationResource([
            'financed_amount' => round($financed_amount, 2),
            'service_charge' => round($service_charge, 2),
            'processing_fee' => round($processing_fee, 2),
            'emi' => $emi,
            'tenure_months' => $tenure->months,
            'bank_name' => $bank->name,
        ]);
    }
}
