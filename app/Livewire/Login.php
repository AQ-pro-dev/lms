<?php

namespace App\Livewire;

use App\Mail\PasswordResetMail;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Login extends Component
{
    use LivewireAlert;

    public $email, $password, $reset_email;

    public function login()
    {
        $validatedData = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $this->email)->first();

        if ($user && Hash::check($this->password, $user->password)) {
            if ($user->hasVerifiedEmail()) {
                Auth::login($user);

                session([
                    'user_id' => $user->id,
                    // 'role_ids' => $user->roles()->pluck('id')->toArray(),
                ]);


                return redirect()->route('dashboard.settings');
            } else {
                $this->alert('warning', 'Please verify your email address before logging in.');

                return;
            }
        }

        $this->alert('warning', 'Invalid email or password. Please try again.');
    }


    public function sendPasswordResetLink(Request $request)
    {
        $this->validate([
            'reset_email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $this->reset_email)->first();

        if (!$user) {
            $this->alert('error', 'No user found with this email address.');
            return;
        }

        $token = bin2hex(random_bytes(32));
        $user->password_reset_token = $token;
        $user->password_token_created_at = now();
        $user->save();


        $resetUrl = route('password.reset', ['token' => $token, 'email' => $user->email]);


        // Send email (customized)
        Mail::to($user->email)->send(new PasswordResetMail($resetUrl));

        $this->alert('success', 'Password reset link sent!');
        $this->dispatch('close-modal');
        $this->reset('reset_email');
    }



    public function render()
    {
        return view('livewire.login');
    }
}
