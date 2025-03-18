<?php

namespace Database\Seeders;

use App\Models\Claim;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClaimSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get available donations and charity users
        $availableDonations = Donation::where('status', 'available')->get();
        $charities = User::where('role', 'charity')->get();

        // Get test accounts
        $testCharity = User::where('email', 'charity@example.com')->first();
        $testDonorDonation = Donation::where('title', 'Test Donation Item')->first();

        // Create a claim for the test donation by the test charity
        if ($testCharity && $testDonorDonation) {
            Claim::factory()->create([
                'donation_id' => $testDonorDonation->id,
                'charity_id' => $testCharity->id,
                'status' => 'pending',
                'notes' => 'This is a test claim for demonstration purposes.',
            ]);
        }

        // Create claims for other available donations
        foreach ($availableDonations as $donation) {
            // Skip the test donation (already handled above)
            if ($donation->title === 'Test Donation Item') {
                continue;
            }

            // Randomly determine if this donation will have claims (70% chance)
            if (rand(1, 10) <= 7) {
                // Randomly determine how many charities will claim this donation (1-3)
                $claimCount = rand(1, min(3, count($charities)));
                $randomCharities = $charities->random($claimCount);

                foreach ($randomCharities as $charity) {
                    // Create claim with a random status
                    Claim::factory()->create([
                        'donation_id' => $donation->id,
                        'charity_id' => $charity->id,
                    ]);
                }

                // If any claim was approved, update the donation status to claimed
                $approvedClaim = Claim::where('donation_id', $donation->id)
                    ->where('status', 'approved')
                    ->first();

                if ($approvedClaim) {
                    $donation->update(['status' => 'claimed']);
                }
            }
        }

        // Create some completed claims for historical data
        $completedDonations = Donation::where('status', 'completed')->get();
        foreach ($completedDonations as $donation) {
            $randomCharity = $charities->random();
            Claim::factory()->completed()->create([
                'donation_id' => $donation->id,
                'charity_id' => $randomCharity->id,
            ]);
        }
    }
}
