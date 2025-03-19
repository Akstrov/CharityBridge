<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Notifications\NewMessageNotification;

class MessageController extends Controller
{
    public function index(Claim $claim)
    {
        $this->authorize('view', $claim);

        $messages = $claim->messages()
            ->with('sender:id,name,role')
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('Charity/Claims/Messages/Index', [
            'claim' => $claim->load(['donation.donor', 'charity']),
            'messages' => $messages,
        ]);
    }

    public function store(Request $request, Claim $claim)
    {
        $this->authorize('sendMessage', $claim);

        $validated = $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $message = $claim->messages()->create([
            'sender_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        // Mark all previous messages as read
        $claim->messages()
            ->where('sender_id', '!=', auth()->id())
            ->update(['is_read' => true]);

        // Notify the other party
        $recipient = auth()->id() === $claim->donation->user_id ? $claim->charity : $claim->donation->user;
        $recipient->notify(new NewMessageNotification($message, $claim));

        return back()->with('success', 'Message sent successfully.');
    }

    public function markAsRead(Message $message)
    {
        $this->authorize('update', $message);

        $message->update(['is_read' => true]);

        return back();
    }
}
