<section class="register">
    <div class="container-fluid">
        <h1>REGISTER TODAY</h1>
        <div class="register_text text-center mb-4">
            <h2>SIGN UP & START LEARNING</h2>
            <p>We have the best specialists in your region. Quality and professionalism is our slogan</p>
        </div>

        <form wire:submit.prevent="register" style="position: relative" class="form w-75 w-lg-50 w-md-75 w-sm-100 mx-auto">
            <div wire:loading wire:target="register" class="loading-spinner">
                <div class="spinner"></div>
            </div>
            <div class="register_form">
                <h6 class="text-center">Your Information</h6>
                <div class="row pt-3">
                    <div class="col-12 col-md-6">
                        <label for="first_name">First Name</label>
                        <input wire:model="first_name" class="form-control" type="text" name="first_name">
                        @error('first_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="last_name">Last Name</label>
                        <input wire:model="last_name" class="form-control" type="text" name="last_name">
                        @error('last_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row pt-3">
                    <div class="col-12 col-md-6">
                        <label for="email">Email</label>
                        <input wire:model="email" class="form-control" type="email" name="email">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="phone">Phone</label>
                        <input wire:model="phone" class="form-control" type="tel" name="phone">
                        @error('phone')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row pt-3">
                    <div class="col-12 col-md-6">
                        <label for="password">Password</label>
                        <input wire:model="password" class="form-control" type="password" name="password">
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label for="confirm_password">Re-type Password</label>
                        <input wire:model="confirm_password" class="form-control" type="password"
                            name="confirm_password">
                        @error('confirm_password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="row pt-3">
                    <div class="col-12">
                        <div>
                            <label>By signing up, I agree with the website's Terms and Conditions</label>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 d-flex flex-column py-3 gap-3">
                        <button type="submit" class="button-primary">Register</button>

                        <a type="button" href='{{ route('microsoft.redirect') }}'
                            class="button-secondary d-flex align-items-center justify-content-center">
                            <img src="{{ asset('assets/svgs/microsift-icon.svg') }}" alt="">
                            <span class="ms-2">Sign in with Microsoft</span>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
