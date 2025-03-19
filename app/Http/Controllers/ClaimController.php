<?php

namespace App\Http\Controllers;

use App\Models\Claim;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClaimController extends Controller
{
    /**
     * Store a new claim when a charity clicks the Claim button.
     */
    public function store(Donation $donation)
    {
        // Ensure only charities can claim donations
        if (Auth::user()->role !== 'charity') {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        // Ensure the donation is still available
        if ($donation->status !== 'available') {
            return redirect()->back()->with('error', 'This donation is no longer available.');
        }

        // Check if the charity already has a pending or approved claim for this donation
        if ($donation->claims()->where('charity_id', Auth::id())->whereIn('status', ['pending', 'approved'])->exists()) {
            return redirect()->back()->with('error', 'You already have a claim for this donation.');
        }

        // Create a new claim
        $claim = Claim::create([
            'donation_id' => $donation->id,
            'charity_id' => Auth::id(),
            'status' => 'pending',
        ]);

        return redirect()->route('charity.claims.show', $claim->id)
            ->with('success', 'Claim request sent successfully!');
    }

    /**
     * Admin approves a claim.
     */
    public function approve(Claim $claim)
    {
        // Ensure the donation is still available before approval
        if ($claim->donation->status !== 'available') {
            return redirect()->back()->with('error', 'This donation is no longer available.');
        }

        // Reject any other pending claims for this donation
        Claim::where('donation_id', $claim->donation_id)
            ->where('status', 'pending')
            ->update(['status' => 'rejected']);

        // Approve this claim and update the donation status
        $claim->update(['status' => 'approved']);
        $claim->donation->update(['status' => 'claimed']);

        return redirect()->back()->with('success', 'Claim approved!');
    }

    /**
     * Admin rejects a claim.
     */
    public function reject(Claim $claim)
    {
        $claim->update(['status' => 'rejected']);
        return redirect()->back()->with('success', 'Claim rejected.');
    }

    /**
     * Mark a claim as completed.
     */
    public function complete(Claim $claim)
    {
        // Ensure the claim is approved before marking it completed
        if ($claim->status !== 'approved') {
            return redirect()->back()->with('error', 'Only approved claims can be completed.');
        }

        // Complete the claim and update the donation status
        $claim->update(['status' => 'completed']);
        $claim->donation->update(['status' => 'completed']);

        return redirect()->back()->with('success', 'Donation successfully completed.');
    }
}
