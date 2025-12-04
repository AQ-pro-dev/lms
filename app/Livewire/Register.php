<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Mail\EmailVerificationMail;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class Register extends Component
{
    use LivewireAlert;

    public $first_name, $last_name, $email, $phone, $password, $confirm_password;

    public function register()
    {
        // dd(request()->all());
        $this->validate([
            'first_name' => 'required|string|max:20',
            'last_name' => 'required|string|max:20',
            'email' => 'required|email|unique:users|max:100',
            'phone' => 'required|string|max:20',
            'password' => 'required|min:8|same:confirm_password',
        ]);

        try {
            DB::beginTransaction();

            $verificationToken = Str::random(60);

            $role_id = 3;

            $user = User::create([
                'first_name' => $this->first_name,
                'last_name' => $this->last_name,
                'email' => $this->email,
                'phone' => $this->phone,
                'password' => Hash::make($this->password),
                'verification_token' => $verificationToken,
                'microsoft_account' => false,
                'role_id' => 3,
                'timezone' => 'UTC',
            ]);


            $this->sendVerificationEmail($user);
            // dd(request());
            DB::commit();
            $this->alert('success', 'Registration successful! Please check your email to verify your account.');
            Notification::create([
                'user_id' => $user->id,
                'message' => '🎉 Welcome to our platform! We are thrilled to have you on board. 🚀',
                'type' => 'general',
            ]);
            $this->resetForm();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', 'Something went wrong. Please try again.');
            return;
        }
    }

    protected function sendVerificationEmail(User $user)
    {
        $verificationUrl = route('verify.email', ['token' => $user->verification_token]);
        Mail::to($user->email)->send(new EmailVerificationMail($verificationUrl));
    }
    private function resetForm()
    {
        $this->reset(['first_name', 'last_name', 'email', 'phone', 'password', 'confirm_password']);
    }


    public function render()
    {
        return view('livewire.register');
    }
}
