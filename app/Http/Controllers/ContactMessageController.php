<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Enums\ContactMessageStatus;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');

        $messages = ContactMessage::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%")
                        ->orWhere('contact_number', 'like', "%{$q}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.contact_messages.index', compact('messages', 'q'));
    }

    public function show($id)
    {
        $message = ContactMessage::findOrFail($id);
        // Mark as read if not already
        if ($message->status === ContactMessageStatus::UNREAD) {
            $message->update(['status' => ContactMessageStatus::READ]);
        }
        return view('admin.contact_messages.show', compact('message'));
    }

    public function markContacted($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->update(['status' => ContactMessageStatus::ANSWERED]);
        return redirect()->route('contact-message.show', $id)->with('success', 'Message marked as contacted');
    }

    public function destroy($id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();
        return redirect()->route('contact-messages')->with('success', 'Message deleted');
    }
}
