<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    /**
     * Public: contact form se message bheja jata hai
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $contactMessage = ContactMessage::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Your message has been sent successfully.',
            'data' => $contactMessage,
        ], 201);
    }

    /**
     * Admin: saare messages ki listing
     */
    public function index()
    {
        $messages = ContactMessage::latest()->paginate(15);

        return response()->json([
            'status' => true,
            'data' => $messages,
        ], 200);
    }

    /**
     * Admin: ek specific message dekhna
     */
    public function show(string $id)
    {
        $message = ContactMessage::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $message,
        ], 200);
    }

    /**
     * Admin: status update karna (pending/read/replied)
     */
    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:pending,read,replied',
        ]);

        $message = ContactMessage::findOrFail($id);
        $message->update(['status' => $request->status]);

        return response()->json([
            'status' => true,
            'message' => 'Status updated successfully',
            'data' => $message,
        ], 200);
    }

    /**
     * Admin: message delete karna
     */
    public function destroy(string $id)
    {
        $message = ContactMessage::findOrFail($id);
        $message->delete();

        return response()->json([
            'status' => true,
            'message' => 'Message deleted successfully',
        ], 200);
    }
}