<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\CharityDetail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => 'required|in:donor,charity',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            // Charity specific validation
            'organization_name' => 'required_if:role,charity|nullable|string|max:255',
            'description' => 'required_if:role,charity|nullable|string',
            'category' => 'required_if:role,charity|nullable|string|max:100',
            'website' => 'nullable|url|max:255',
            'registration_number' => 'nullable|string|max:100',
            'tax_id' => 'nullable|string|max:100',
            'mission_statement' => 'nullable|string',
            'areas_of_focus' => 'nullable|string',
            'year_established' => 'nullable|integer|min:1800|max:' . date('Y'),
            'social_media_links' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'phone' => $request->phone,
            'address' => $request->address,
        ]);

        // If the user is a charity, create charity details
        if ($request->role === 'charity') {
            CharityDetail::create([
                'user_id' => $user->id,
                'organization_name' => $request->organization_name,
                'description' => $request->description,
                'category' => $request->category,
                'website' => $request->website,
                'registration_number' => $request->registration_number,
                'tax_id' => $request->tax_id,
                'mission_statement' => $request->mission_statement,
                'areas_of_focus' => $request->areas_of_focus,
                'year_established' => $request->year_established,
                'social_media_links' => $request->social_media_links,
            ]);
        }

        event(new Registered($user));

        Auth::login($user);

        // Redirect based on role
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->isCharity()) {
            return redirect()->route('charity.dashboard');
        } else {
            return redirect()->route('donor.dashboard');
        }
    }
}
