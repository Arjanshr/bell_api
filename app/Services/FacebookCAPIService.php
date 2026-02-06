<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class FacebookCAPIService
{
    protected string $pixel_id;
    protected string $access_token;

    public function __construct()
    {
        $this->pixel_id = config('services.facebook.pixel_id');
        $this->access_token = config('services.facebook.access_token');
    }

    public function sendEvent(array $data)
    {
        $url = "https://graph.facebook.com/v18.0/{$this->pixel_id}/events";

        $payload = [
            'data' => [$data],
            'access_token' => $this->access_token,
        ];

        return Http::post($url, $payload)->json();
    }

    public function hashData($value)
    {
        return hash('sha256', strtolower(trim($value)));
    }
}
