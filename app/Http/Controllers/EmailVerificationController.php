<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EmailVerificationController extends Controller
{
    public function verify($token)
    {
        $user = User::where('verification_token', $token)->first();

        if (!$user) {
            Session::flash('error', 'Invalid or expired verification link.');
            return redirect()->route('login');
        }

        $user->update([
            'email_verified_at' => now(),
            'verification_token' => null,
        ]);

        Session::flash('success', 'Your email has been verified! You can now log in.');
        return redirect()->route('login');
    }
}
