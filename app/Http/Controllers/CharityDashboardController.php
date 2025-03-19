<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Donation;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class CharityDashboardController extends Controller
{
    /**
     * Display the charity dashboard.
     */
    public function index(): Response
    {
        $charity = Auth::user();

        // Get stats
        $stats = [
            'submittedClaims' => $charity->claims()->count(),
            'approvedClaims' => $charity->claims()->where('status', 'approved')->count(),
            'pendingApproval' => $charity->claims()->where('status', 'pending')->count(),
            'availableDonations' => Donation::where('status', 'available')->count(),
        ];

        // Get recent claims with donor information
        $recentClaims = $charity->claims()
            ->with('donation.donor')
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($claim) {
                return [
                    'id' => $claim->id,
                    'title' => $claim->donation->title,
                    'status' => $claim->status,
                    'date' => $claim->created_at->format('Y-m-d'),
                    'donor' => $claim->donation->donor->name,
                ];
            });

        return Inertia::render('Charity/Dashboard', [
            'stats' => $stats,
            'recentClaims' => $recentClaims,
        ]);
    }

    /**
     * Display all claims for the charity.
     */
    public function claims(Request $request): Response
    {
        $charity = Auth::user();

        $query = $charity->claims()
            ->with('donation.donor')
            ->latest();

        // Apply status filter if provided
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $claims = $query->paginate(10)
            ->through(function ($claim) {
                return [
                    'id' => $claim->id,
                    'title' => $claim->donation->title,
                    'status' => $claim->status,
                    'date' => $claim->created_at->format('Y-m-d'),
                    'donor' => $claim->donation->donor->name,
                ];
            });

        return Inertia::render('Charity/Claims/Index', [
            'claims' => $claims,
            'filters' => [
                'status' => $request->status ?? 'all',
            ],
        ]);
    }

    /**
     * Delete a claim.
     */
    public function destroy(Claim $claim)
    {
        // Ensure the authenticated user is the charity that made the claim
        if ($claim->charity_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Only allow deletion of pending claims
        if ($claim->status !== 'pending') {
            abort(400, 'Only pending claims can be cancelled.');
        }

        $claim->delete();

        return redirect()->route('charity.claims.index')->with('success', 'Claim cancelled successfully.');
    }

    /**
     * Display claim details.
     */
    public function show(Claim $claim): Response
    {
        // Ensure the authenticated user is the charity that made the claim
        if ($claim->charity_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $claim->load(['donation.donor']);

        return Inertia::render('Charity/Claims/Show', [
            'claim' => [
                'id' => $claim->id,
                'title' => $claim->donation->title,
                'status' => $claim->status,
                'date' => $claim->created_at->format('Y-m-d'),
                'donor' => $claim->donation->donor->name,
                'donor_email' => $claim->donation->donor->email,
                'category' => $claim->donation->category,
                'description' => $claim->donation->description,
                'amount' => $claim->donation->monetary_value,
                'currency' => 'USD',
                'donation_date' => $claim->donation->created_at->format('Y-m-d'),
                'claim_date' => $claim->created_at->format('Y-m-d'),
                'claim_notes' => $claim->notes,
            ],
        ]);
    }
} 