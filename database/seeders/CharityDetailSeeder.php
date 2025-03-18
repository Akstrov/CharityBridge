<?php

namespace Database\Seeders;

use App\Models\CharityDetail;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CharityDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        // Get all charity users without charity details
        $charityUsers = User::where('role', 'charity')
            ->whereDoesntHave('charityDetail')
            ->get();

        // Create charity details for test charity
        $testCharity = User::where('email', 'charity@example.com')->first();
        if ($testCharity) {
            CharityDetail::factory()->create([
                'user_id' => $testCharity->id,
                'organization_name' => 'Helping Hands Foundation',
                'mission_statement' => 'We aim to make a difference in our community by connecting donors with those in need.',
                'verified' => true,
                'category' => 'community_development',
            ]);
        }

        // Create charity details for other charity users
        foreach ($charityUsers as $charityUser) {
            // Skip if it's the test charity (already handled above)
            if ($charityUser->email === 'charity@example.com') {
                continue;
            }

            CharityDetail::factory()->create([
                'user_id' => $charityUser->id,
            ]);
        }
    }
}
