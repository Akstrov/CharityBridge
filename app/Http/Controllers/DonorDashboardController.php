<?php

namespace App\Http\Controllers;

use App\Models\Donation;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DonorDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $donations = $user->donations()->with('claims')->latest()->take(3)->get();

        // Calculate statistics
        $stats = [
            'totalDonations' => $user->donations()->count(),
            'activeDonations' => $user->donations()->where('status', 'available')->count(),
            'claimedDonations' => $user->donations()->where('status', 'claimed')->count(),
            'completedDonations' => $user->donations()->where('status', 'completed')->count(),
        ];

        // Format recent donations
        $recentDonations = $donations->map(function ($donation) {
            return [
                'id' => $donation->id,
                'title' => $donation->title,
                'status' => $donation->status,
                'date' => $donation->created_at->format('Y-m-d'),
                'claims' => $donation->claims->count(),
            ];
        });

        return Inertia::render('Donor/Dashboard', [
            'stats' => $stats,
            'recentDonations' => $recentDonations,
        ]);
    }
} 