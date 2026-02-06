<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CampaignResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Start with common data
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'color_theme' => $this->color_theme,
            'background_image' => $this->background_image ? asset('storage/' . $this->background_image) : null,
            'campaign_banner' => $this->campaign_banner ? asset('storage/' . $this->campaign_banner) : null,
            'banner_url' => $this->banner_url ?? null, // New banner URL field
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
        ];

        // Determine the campaign status based on start_date and end_date
        $status = $this->getCampaignStatus();

        // Add time fields based on the determined status
        if ($status === 'active') {
            $data["time_since_started"] = $this->time_since_started;
            $data["time_until_expiry"] = $this->time_until_expiry;
        } elseif ($status === 'future') {
            $data["time_until_start"] = $this->time_until_start;  // Add for future campaigns
        } elseif ($status === 'expired') {
            $data["time_since_expired"] = $this->time_since_expired;  // Add for expired campaigns
        }

        return $data;
    }

    /**
     * Helper method to determine the campaign's status based on the dates.
     */
    private function getCampaignStatus(): string
    {
        if ($this->start_date <= now() && $this->end_date >= now()) {
            return 'active'; // Campaign is running
        } elseif ($this->start_date > now()) {
            return 'future'; // Campaign is in the future
        } else {
            return 'expired'; // Campaign is expired
        }
    }
}
