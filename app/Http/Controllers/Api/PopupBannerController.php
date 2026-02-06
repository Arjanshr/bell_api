<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PopupBannerResource;
use App\Models\PopupBanner;
use Illuminate\Http\Request;

class PopupBannerController extends BaseController
{
    /**
     * Get the first popup banner.
     *
     * @return \Illuminate\Http\Response
     */
    public function first()
    {
        $popupBanner = PopupBanner::first();  // Fetch the first popup banner

        if ($popupBanner) {
            return $this->sendResponse(new PopupBannerResource($popupBanner), 'Popup banner retrieved successfully.');
        }

        return $this->sendError('Popup banner not found', 'No popup banner available', 404);
    }
}
