<?php

namespace App\Http\Controllers\Donor;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Message;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function index(Claim $claim)
    {
        $this->authorize('view', $claim);

        $messages = $claim->messages()
            ->with('sender:id,name,role')
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('Donor/Claims/Messages/Index', [
            'claim' => $claim->load(['donation', 'charity']),
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
            'content' => $validated['content'],
            'sender_id' => auth()->id(),
        ]);

        return back();
    }

    public function markAsRead(Claim $claim)
    {
        $this->authorize('view', $claim);

        $claim->messages()
            ->where('sender_id', '!=', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return back();
    }
} 