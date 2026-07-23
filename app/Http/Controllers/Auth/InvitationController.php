<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class InvitationController extends Controller
{
    /** Show the accept-invitation form (or an error when the link is invalid). */
    public function show(string $token)
    {
        $invitation = TeamInvitation::with('company', 'inviter')->where('token', $token)->first();

        if (! $invitation || ! $invitation->isPending()) {
            return view('auth.invitation', [
                'invitation' => $invitation,
                'invalid' => true,
            ]);
        }

        return view('auth.invitation', [
            'invitation' => $invitation,
            'invalid' => false,
        ]);
    }

    /** Accept the invitation: create the user in the company and log them in. */
    public function store(Request $request, string $token)
    {
        $invitation = TeamInvitation::where('token', $token)->first();

        if (! $invitation || ! $invitation->isPending()) {
            return redirect()->route('login')->with('status', 'Deze uitnodiging is niet meer geldig.');
        }

        if (User::whereRaw('LOWER(email) = ?', [mb_strtolower($invitation->email)])->exists()) {
            return redirect()->route('login')->with('status', 'Er bestaat al een account met dit e-mailadres. Log in.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $invitation->email,
            'password' => Hash::make($data['password']),
            'company_id' => $invitation->company_id,
            'role' => $invitation->role,
            'email_verified_at' => now(),
        ]);

        $invitation->update(['accepted_at' => now()]);

        event(new Registered($user));
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
