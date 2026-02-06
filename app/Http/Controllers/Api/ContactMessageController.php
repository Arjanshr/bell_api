<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreContactMessageRequest;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    /**
     * Store a newly created contact message.
     */
    public function store(StoreContactMessageRequest $request)
    {
        $data = $request->validated();

        $contactMessage = ContactMessage::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Contact message received',
            'data' => $contactMessage,
        ], 201);
    }
}
