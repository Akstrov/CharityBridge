<?php

namespace Database\Seeders;

use App\Models\Donation;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DonationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get donor users
        $donors = User::where('role', 'donor')->get();

        // Create test donation for the test donor
        $testDonor = User::where('email', 'donor@example.com')->first();
        if ($testDonor) {
            Donation::factory()->create([
                'user_id' => $testDonor->id,
                'title' => 'Test Donation Item',
                'description' => 'This is a test donation for demonstration purposes.',
                'category' => 'food',
                'quantity' => 25,
                'status' => 'available',
                'is_urgent' => true,
            ]);

            // Create some additional donations for test donor
            Donation::factory()->count(4)->create([
                'user_id' => $testDonor->id
            ]);
        }

        // Create donations for each donor (3 per donor)
        foreach ($donors as $donor) {
            // Skip if it's the test donor (already handled above)
            if ($donor->email === 'donor@example.com') {
                continue;
            }

            // Create varied donations
            Donation::factory()->create([
                'user_id' => $donor->id,
                'category' => 'food',
                'status' => 'available',
            ]);

            Donation::factory()->create([
                'user_id' => $donor->id,
                'category' => 'clothes',
                'status' => 'available',
            ]);

            Donation::factory()->create([
                'user_id' => $donor->id,
                'category' => 'money',
                'status' => 'available',
            ]);
        }

        // Create some completed donations for historical data
        Donation::factory()->count(10)->create([
            'status' => 'completed'
        ]);
    }
}
