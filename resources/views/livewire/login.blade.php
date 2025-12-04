<section class="login">
    <div class="container-fluid">
        <h1>Login</h1>
        <form wire:submit.prevent="login" class="form position-relative w-50">
            <div class="login_form">
                <div wire:loading wire:target="login" class="loading-spinner">
                    <div class="spinner"></div>
                </div>
                @if (session('status'))
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->has('msg'))
                    <div class="alert alert-warning">
                        {{ $errors->first('msg') }}
                    </div>
                @endif





                @if (session()->has('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                <div class="row pt-3 mb-2">
                    <div class="col mt-4">
                        <label for="email">Email</label>
                        <input wire:model="email" class="form-control mt-2" type="text" name="email">
                        @error('email')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <label for="password">Password</label>
                        <input wire:model="password" class="form-control mt-2" type="password" name="password">
                        @error('password')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <div class="text-end mt-2 mb-4">
                            <button type="button" class="btn btn-link forgot-btn" data-bs-toggle="modal"
                                data-bs-target="#forgotPasswordModal">
                                Forgot Password?
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col d-flex flex-column py-3 gap-3">
                        <button type="submit" class="button-primary">Login</button>
                        <a href='{{ route('microsoft.redirect') }}' type="button" class="button-secondary text-center">
                            <img src="{{ asset('assets/svgs/microsift-icon.svg') }}" alt="">
                            <span class="ms-2">Continue with Microsoft</span>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <!-- Forgot Password Modal -->
    <div wire:ignore.self class="modal fade" id="forgotPasswordModal" tabindex="-1"
        aria-labelledby="forgotPasswordModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="forgotPasswordModalLabel">Forgot Password</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="sendPasswordResetLink">
                        <div class="mb-3">
                            <label for="reset_email" class="form-label">Enter your email address</label>
                            <input type="email" wire:model="reset_email" class="form-control" id="reset_email"
                                required>
                            @error('reset_email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <button type="submit" class="btn button-primary">Send Reset Link</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


</section>

@script
    <script>
        window.addEventListener('close-modal', event => {

            $('#forgotPasswordModal').modal('hide');
        })
    </script>
@endscript
