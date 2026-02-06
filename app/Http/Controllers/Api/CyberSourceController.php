<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CyberSourceController extends Controller
{

    protected function getSecretKey()
    {
        $mode = Setting::where('key', 'cybersource_mode')->value('value');

        return $mode === 'test'
            ? env('CYBERSOURCE_TEST_SECRET_KEY')
            : env('CYBERSOURCE_MAIN_SECRET_KEY');
    }
    /**
     * Generate CyberSource signature for Secure Acceptance form.
     */

    public function generateSignature(Request $request)
    {
        $signed_array = explode(',', $request->signed_field_names);
        $signed_string = '';
        foreach ($signed_array as $key => $value) {
            $key_val = $value . '=' . $request[$value];
            if ($key == 0)
                $signed_string = $key_val;
            else
                $signed_string = $signed_string . ',' . $key_val;
        }
        $hash_code = hash_hmac('sha256', $signed_string, $this->getSecretKey(), true);
        $hash_encode = base64_encode($hash_code);
        return response()->json(['signature' => $hash_encode]);
    }

    /**
     * Handle transaction response from CyberSource (Merchant POST URL).
     */
    public function handleResponse(Request $request)
    {
        $fields = explode(',', $request->input('signed_field_names'));
        $data = [];

        foreach ($fields as $field) {
            $data[] = $field . '=' . $request->input($field, '');
        }

        $signedString = implode(',', $data);
        $expectedSignature = base64_encode(hash_hmac(
            'sha256',
            $signedString,
            $this->getSecretKey(),
            true
        ));

        $isValid = hash_equals($expectedSignature, $request->input('signature'));

        // Parse reference number to get order ID
        $ref = $request->input('req_reference_number');
        $orderId = null;
        if (str_starts_with($ref, 'ORDER-')) {
            $orderId = (int) str_replace('ORDER-', '', $ref);
        }

        $order = $orderId ? Order::find($orderId) : null;

        // Logging for debugging
        Log::info('CyberSource Response Received', [
            'valid_signature' => $isValid,
            'decision' => $request->input('decision'),
            'reason_code' => $request->input('reason_code'),
            'reference_number' => $ref,
            'transaction_id' => $request->input('transaction_id'),
            'amount' => $request->input('req_amount'),
            'order_found' => !!$order,
        ]);

        // If valid + accepted + order found → mark as paid
        if ($isValid && $order && $request->input('decision') === 'ACCEPT' && $request->input('reason_code') == '100') {
            $order->payment_status = 'paid';
            $order->payment_reference = $request->input('transaction_id');
            $order->paid_at = now();
            $order->save();

            Log::info("Order #$orderId marked as paid via CyberSource.");
            return response('Transaction Verified', 200);
        }

        // Handle error: invalid signature or missing order
        $order->status = 'cancelled';
        $order->save();
        Log::warning('CyberSource transaction failed or invalid', [
            'order_id' => $orderId,
            'reason' => !$isValid ? 'Invalid signature' : 'Not ACCEPT or missing order'
        ]);

        return response('Invalid or failed transaction', 400);
    }
    /**
     * Handle CyberSource callback for payment status.
     */
    public function callback(Request $request)
    {
        // Log the callback data for debugging
        Log::info('CyberSource Callback Received', $request->all());

        // Validate the request signature
        $signature = $request->input('signature');
        if (!$signature) {
            Log::error('CyberSource Callback: Missing signature');
            return response('Invalid request', 400);
        }

        // Generate expected signature
        $expectedSignature = $this->generateSignature($request)->getData()->signature;

        // Compare signatures
        if (!hash_equals($expectedSignature, $signature)) {
            Log::error('CyberSource Callback: Signature mismatch');
            return response('Invalid signature', 400);
        }


        return response('Callback processed successfully', 200);
    }
}
