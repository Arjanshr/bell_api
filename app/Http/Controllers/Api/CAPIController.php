<?php
namespace App\Http\Controllers\Api;

use App\Services\FacebookCAPIService;
use Illuminate\Http\Request;

class CAPIController extends BaseController
{
    public function handle(Request $request, FacebookCAPIService $fb)
    {
        $response = $fb->sendEvent([
            'event_name' => $request->input('event_name'),
            'event_time' => time(),
            'event_source_url' => $request->headers->get('referer'),
            'action_source' => 'website',
            'event_id' => uniqid(), // for deduplication
            'user_data' => [
                'em' => $fb->hashData($request->input('email')),
                'ph' => $fb->hashData($request->input('phone')),
                '_fbp' => $request->input('fbp'),
                'client_ip_address' => $request->ip(),
                'client_user_agent' => $request->userAgent(),
            ],
            'custom_data' => [
                'currency' => $request->input('currency', 'USD'),
                'value' => $request->input('value', 0),
            ],
        ]);

        return response()->json($response);
    }
}
