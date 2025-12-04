<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class PasswordResetController extends Controller
{
    public function showResetForm(Request $request, $token)
    {
        return view('emails.password-reset', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ]);

        $user = User::where('email', $request->email)
            ->where('password_reset_token', $request->token)
            ->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Invalid or expired token.']);
        }

        $user->password = Hash::make($request->password);
        $user->password_reset_token = null;
        $user->password_token_created_at = null;
        $user->save();

        return redirect()->route('login')->with('status', 'Password reset successfully!');
    }
}
