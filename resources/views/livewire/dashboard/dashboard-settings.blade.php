<div class="settings px-4 py-5">
    @push('title')
        Settings
    @endpush
    <div class="page-head d-flex justify-content-between">
        <h2 class="mb-4">Settings</h2>
        @if (!Auth::user()->microsoft_account)
            <div class="ms-button">
                <a type="button" href='{{ route('microsoft.redirect') }}'
                    class="button-secondary d-flex align-items-center justify-content-center">
                    <img src="{{ asset('assets/svgs/microsift-icon.svg') }}" alt="">
                    <span class="ms-2">Connect with Microsoft</span>
                </a>
            </div>
        @endif
    </div>
    <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'profile' ? 'active' : '' }}" id="profile-tab" data-bs-toggle="tab"
                href="#profile" role="tab" aria-controls="profile" aria-selected="true"
                wire:click="changeTab('profile')">Profile</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'password' ? 'active' : '' }}" id="password-tab" data-bs-toggle="tab"
                href="#password" role="tab" aria-controls="password" aria-selected="false"
                wire:click="changeTab('password')">Password</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $activeTab == 'social-profile' ? 'active' : '' }}" id="social-profile-tab"
                data-bs-toggle="tab" href="#social-profile" role="tab" aria-controls="social-profile"
                aria-selected="false" wire:click="changeTab('social-profile')">Social Profile</a>
        </li>
    </ul>

    <div class="tab-content">
        <!-- Profile Tab -->

        <div id="profile" class="tab-pane fade {{ $activeTab == 'profile' ? 'show active' : '' }}" role="tabpanel"
            aria-labelledby="profile-tab">
            {{-- Cover Photo Section --}}
            <div class="wrapper position-relative">
                <div class="cover-photo-wrapper">
                    @if ($coverPhoto)
                        <img src="{{ $coverPhoto->temporaryUrl() }}" class="cover-photo" alt="Cover Photo">
                    @elseif ($user->cover_photo)
                        <img src="{{ asset('storage/' . $user->cover_photo) }}" class="cover-photo" alt="Cover Photo">
                    @else
                        <img src="{{ asset('assets/images/dummy-cover-photo.webp') }}" class="cover-photo"
                            alt="Cover Photo">
                    @endif
                    <button type="button" wire:click="deleteCoverPhoto" class="btn btn-danger btn-sm position-absolute"
                        style="top: 10px; right: 10px;">
                        <i class="fas fa-trash"></i>
                    </button>
                    <label for="coverPhotoUpload" class="btn btn-success btn-sm position-absolute"
                        style="bottom: 10px; right: 10px;">
                        <i class="fas fa-upload"></i> <span class="update-label
                        ">Update Cover
                            Photo</span>
                    </label>
                    <input type="file" id="coverPhotoUpload" wire:model="coverPhoto" class="file-input d-none"
                        accept="image/*">
                </div>

                <div class="profile-picture">
                    <div class="profile-img">
                        @if ($profilePhoto)
                            <img src="{{ $profilePhoto->temporaryUrl() }}" class="profile-photo rounded-circle"
                                alt="Profile Photo">
                        @elseif ($user->pfp)
                            <img src="{{ asset('storage/' . $user->pfp) }}" class="profile-photo rounded-circle"
                                alt="Profile Photo">
                        @else
                            <img src="{{ asset('assets/images/dummy-profile-photo.webp') }}"
                                class="profile-photo rounded-circle" alt="Profile Photo">
                        @endif
                        <button class="btn btn-light btn-circle" id="profileDropdown" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fas fa-camera"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="profileDropdown">
                            <li>
                                <label for="profilePhotoUpload" class="dropdown-item">
                                    <i class="fas fa-upload"></i> Upload Photo
                                </label>
                                <input type="file" id="profilePhotoUpload" wire:model="profilePhoto"
                                    class="file-input d-none" accept="image/*">
                            </li>
                            <li>
                                <button type="button" wire:click="deleteProfilePhoto"
                                    class="dropdown-item text-danger">
                                    <i class="fas fa-trash"></i> Delete Photo
                                </button>
                            </li>
                        </ul>
                    </div>
                    {{-- </div> --}}
                </div>

            </div>

            <!-- Update Profile Form -->
            <form wire:submit.prevent="updateProfile">
                @error('coverPhoto')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                @error('profilePhoto')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                <div class="form-row">
                    <div class="col-md-6">
                        <label for="firstName">First Name</label>
                        <input type="text" class="form-control" id="firstName" wire:model="firstName">
                        @error('firstName')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="lastName">Last Name</label>
                        <input type="text" class="form-control" id="lastName" wire:model="lastName">
                        @error('lastName')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row mt-3">
                    <div class="col-md-6">
                        <label for="userName">User Name</label>
                        <input type="text" class="form-control" id="userName" wire:model="userName">
                        @error('userName')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="phone">Phone Number</label>
                        <input type="text" class="form-control" id="phone" wire:model="phone">
                        @error('phone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label for="bio">Bio</label>
                    <textarea id="bio" class="form-control" wire:model="bio"></textarea>
                    @error('bio')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mt-3">
                    <label for="timezone">Timezone</label>
                    <select id="timezone" class="form-control" wire:model="timezone">
                        <option value="">Select Timezone</option>

                        @foreach ($timezoneOptions as $tz)
                            <option value="{{ $tz }}" {{ $tz == $timezone ? 'selected' : '' }}>
                                {{ $tz }}
                            </option>
                        @endforeach
                    </select>
                    @error('timezone')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="button-primary mt-3" wire:loading.attr="disabled">
                    <span wire:loading.remove>Update Profile</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Updating...
                    </span>
                </button>
            </form>
        </div>

        <!-- Password Tab -->
        <div id="password" class="tab-pane fade {{ $activeTab == 'password' ? 'show active' : '' }}"
            role="tabpanel" aria-labelledby="password-tab">
            <form wire:submit.prevent="updatePassword">
                <div class="row">
                    <div class="form-group">
                        <label for="currentPassword">Current password</label>
                        <input type="password" id="currentPassword" wire:model="currentPassword"
                            class="form-control" placeholder="Current password">
                        @error('currentPassword')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="newPassword">New password</label>
                        <input type="password" id="newPassword" wire:model="newPassword" class="form-control"
                            placeholder="New password">
                        @error('newPassword')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="newPasswordConfirmation">Confirm New Password</label>
                        <input type="password" id="newPasswordConfirmation" wire:model="newPassword_confirmation"
                            class="form-control" placeholder="Confirm new password">
                        @error('newPassword_confirmation')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Submit Button with Loader -->
                <button type="submit" class=" button-primary" wire:loading.attr="disabled">
                    <span wire:loading.remove>Reset Password</span>
                    <span wire:loading>
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Resetting...
                    </span>
                </button>
            </form>
        </div>



        <!-- Social Profile Tab -->
        <div id="social-profile" class="tab-pane fade {{ $activeTab == 'social-profile' ? 'show active' : '' }}"
            role="tabpanel" aria-labelledby="social-profile-tab">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="facebook" class="d-flex align-items-center">
                        <i class="fa-brands fa-facebook-f me-2"></i> Facebook
                    </label>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="url" class="form-control" id="facebook"
                        placeholder="http://facebook.com/username" wire:model="facebook">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="twitter" class="d-flex align-items-center">
                        <i class="fa-brands fa-twitter me-2"></i> Twitter
                    </label>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="url" class="form-control" id="twitter"
                        placeholder="http://twitter.com/username" wire:model="twitter">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="linkedin" class="d-flex align-items-center">
                        <i class="fa-brands fa-linkedin me-2"></i> Linkedin
                    </label>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="url" class="form-control" id="linkedin"
                        placeholder="http://linkedin.com/username" wire:model="linkedin">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="website" class="d-flex align-items-center">
                        <i class="fa-solid fa-globe me-2"></i> Website
                    </label>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="url" class="form-control" id="website" placeholder="http://example.com"
                        wire:model="website">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="github" class="d-flex align-items-center">
                        <i class="fa-brands fa-github me-2"></i> Github
                    </label>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="url" class="form-control" id="github"
                        placeholder="http://github.com/username" wire:model="github">
                </div>
                <div class="col-md-12 d-flex justify-content-end mt-3">
                    <button class="button-primary" wire:click="updateSocialLinks" wire:loading.attr="disabled">
                        <span wire:loading.remove>Submit</span>
                        <span wire:loading>
                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                            Updating...
                        </span>
                    </button>
                </div>
            </div>
        </div>

    </div>
</div>
