<?php

namespace App\Livewire\Dashboard;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;

class DashboardSettings extends Component
{
    use WithFileUploads, LivewireAlert;

    public $user;
    public $coverPhoto;
    public $profilePhoto;
    public $firstName;
    public $lastName;
    public $userName;
    public $phone;
    public $bio;
    public $currentPassword;
    public $newPassword;
    public $newPassword_confirmation;
    public $timezone;
    public $timezoneOptions;
    public $facebook;
    public $twitter;
    public $linkedin;
    public $website;
    public $github;

    public $activeTab = 'profile';

    public function mount()
    {
        $this->user = Auth::user();
        $this->firstName = $this->user->first_name;
        $this->lastName = $this->user->last_name;
        $this->userName = $this->user->username;
        $this->phone = $this->user->phone;
        $this->bio = $this->user->bio;
        $this->timezone = $this->user->timezone ?? '';
        $this->facebook = $this->user->facebook;
        $this->twitter = $this->user->twitter;
        $this->linkedin = $this->user->linkedin;
        $this->website = $this->user->website;
        $this->github = $this->user->github;
        $this->timezoneOptions = array_values(\DateTimeZone::listIdentifiers(\DateTimeZone::ALL));
        // dd($this->timezoneOptions);
    }


    public function changeTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updateProfile()
    {
        $this->validate([
            'firstName' => 'required|string|max:20',
            'lastName' => 'required|string|max:20',
            'userName' => 'required|string|max:20|unique:users,username,' . $this->user->id,
            'phone' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:255',
            'coverPhoto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:1024',
            'profilePhoto' => 'nullable|image|mimes:jpeg,png,jpgc,webp|max:1024',
            'timezone' => 'required|string|timezone',
        ]);

        if ($this->coverPhoto) {
            $coverPhotoPath = $this->coverPhoto->store('cover_photos', 'public');
            $this->user->cover_photo = $coverPhotoPath;
        }

        if ($this->profilePhoto) {
            $profilePhotoPath = $this->profilePhoto->store('pfps', 'public');
            $this->user->pfp = $profilePhotoPath;
        }

        $this->user->first_name = $this->firstName;
        $this->user->last_name = $this->lastName;
        $this->user->username = $this->userName;
        $this->user->phone = $this->phone;
        $this->user->bio = $this->bio;
        $this->user->timezone = $this->timezone;

        $this->user->save();

        $this->alert('success', 'Profile updated successfully!');
    }

    public function deleteCoverPhoto()
    {
        if ($this->user->cover_photo) {
            Storage::disk('public')->delete($this->user->cover_photo);
            $this->user->cover_photo = null;
            $this->user->save();

            $this->alert('success', 'Cover photo deleted successfully!');
        }
    }

    public function deleteProfilePhoto()
    {
        if ($this->user->pfp) {
            Storage::disk('public')->delete($this->user->pfp);
            $this->user->pfp = null;
            $this->user->save();

            $this->alert('success', 'Profile photo deleted successfully!');
        }
    }

    public function updatePassword()
    {
        $this->validate([
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:8|confirmed',
            'newPassword_confirmation' => 'required|string|min:8',
        ]);

        // Verify current password
        if (!Hash::check($this->currentPassword, $this->user->password)) {
            $this->alert('error', 'Current password is incorrect.');
            return;
        }

        // Update password
        $this->user->password = Hash::make($this->newPassword);
        $this->user->save();

        // Clear password fields after successful update
        $this->currentPassword = '';
        $this->newPassword = '';
        $this->newPassword_confirmation = '';

        $this->alert('success', 'Password updated successfully!');
    }

    public function updateSocialLinks()
    {

        $this->validate([
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'linkedin' => 'nullable|string|max:255',
            'website' => 'nullable|string|max:255',
            'github' => 'nullable|string|max:255',
        ]);

        $this->user->facebook = $this->facebook;
        $this->user->twitter = $this->twitter;
        $this->user->linkedin = $this->linkedin;
        $this->user->website = $this->website;
        $this->user->github = $this->github;
        $this->user->save();

        $this->alert('success', 'Social links updated successfully!');
    }

    public function render()
    {
        return view('livewire.dashboard.dashboard-settings')->layout('components.layouts.dashboard');
    }
}
