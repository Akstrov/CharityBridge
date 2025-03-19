<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DonationController extends Controller
{
    /**
     * Display a listing of the donations.
     */
    public function index(Request $request)
    {
        $query = Donation::with('donor')
            ->where('status', 'available');

        // If the user is a charity, exclude donations they have already claimed
        if (Auth::user()->role === 'charity') {
            $query->whereDoesntHave('claims', function ($q) {
                $q->where('charity_id', Auth::id())
                  ->whereIn('status', ['pending', 'approved']);
            });
        }

        // Apply search filter
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Apply category filter
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        $donations = $query->latest()->paginate(10);
            
        return Inertia::render('Donations/Index', [
            'donations' => $donations,
            'userRole' => Auth::user()->role ?? null,
            'filters' => [
                'search' => $request->search ?? '',
                'category' => $request->category ?? 'all',
            ],
        ]);
    }

    /**
     * Show the form for creating a new donation.
     */
    public function create(Request $request)
    {
        // Check if the user is a donor
        if (Auth::user()->role !== 'donor') {
            abort(403, 'Only donors can create donations.');
        }

        return Inertia::render('Donor/Donations/Create', [
            'from' => $request->query('from', 'available-donations'),
        ]);
    }

    /**
     * Store a newly created donation in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category' => 'required|in:food,clothes,money,other',
            'quantity' => 'nullable|integer|min:1',
            'location' => 'required|string',
            'is_urgent' => 'boolean',
            'monetary_value' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'available';
        $validated['is_urgent'] = $request->has('is_urgent');

        Donation::create($validated);

        // Redirect based on where the user came from
        $from = $request->query('from', 'available-donations');
        if ($from === 'available-donations') {
            return redirect()->route('donations.index')
                ->with('success', 'Donation created successfully!');
        }
        
        return redirect()->route('donor.my-donations')
            ->with('success', 'Donation created successfully.');
    }

    /**
     * Display the specified donation.
     */
    public function show(Donation $donation)
    {
        $donation->load(['donor', 'claims.charity']);

        return Inertia::render('Donations/Show', [
            'donation' => $donation,
            'userRole' => Auth::user()->role ?? null,
        ]);
    }

    /**
     * Show the form for editing the specified donation.
     */
    public function edit(Donation $donation)
    {
        // Check if the authenticated user is the owner of the donation
        if (Auth::id() !== $donation->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        return Inertia::render('Donor/Donations/Edit', [
            'donation' => $donation,
        ]);
    }

    /**
     * Update the specified donation in storage.
     */
    public function update(Request $request, Donation $donation)
    {
        // Check if the authenticated user is the owner of the donation
        if (Auth::id() !== $donation->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'location' => 'required|string',
            'is_urgent' => 'boolean',
            'expiry_date' => 'nullable|date',
        ]);

        $validated['is_urgent'] = $request->has('is_urgent');

        $donation->update($validated);

        return redirect()->route('donor.my-donations')
            ->with('success', 'Donation updated successfully.');
    }

    /**
     * Remove the specified donation from storage.
     */
    public function destroy(Donation $donation)
    {
        // Check if the authenticated user is the owner of the donation
        if (Auth::id() !== $donation->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }

        // Check if the donation can be deleted (only if it's available)
        if ($donation->status !== 'available') {
            return redirect()->route('donations.show', $donation)
                ->with('error', 'Cannot delete a donation that has been claimed or completed.');
        }

        $donation->delete();

        return redirect()->route('donor.my-donations')
            ->with('success', 'Donation deleted successfully.');
    }

    /**
     * Display a listing of the user's donations.
     */
    public function myDonations()
    {
        $user = Auth::user();
        
        $donations = $user->donations()
            ->with('claims')
            ->latest()
            ->get()
            ->map(function ($donation) {
                return [
                    'id' => $donation->id,
                    'title' => $donation->title,
                    'description' => $donation->description,
                    'status' => $donation->status,
                    'created_at' => $donation->created_at,
                    'claims' => $donation->claims->count(),
                ];
            });

        return Inertia::render('Donor/Donations/Index', [
            'donations' => $donations,
        ]);
    }
}
