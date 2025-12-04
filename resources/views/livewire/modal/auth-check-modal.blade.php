<div class="modal fade AuthCheckModal" id="AuthCheckModal" tabindex="-1" aria-labelledby="AuthCheckModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-white" id="AuthCheckModalLabel">Login Required</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="login-options">
                    <!-- Login with Email -->
                    <div class="card login-option mb-3">
                        <a href="{{ route('login') }}">
                            <div class="card-body d-flex align-items-center">
                                <i class="fa-solid fa-envelope me-3" style="font-size: 1.5rem; color: #000;"></i>
                                <p class="mb-0">Login with Email</p>
                            </div>
                        </a>
                    </div>

                    <!-- Login with Microsoft -->
                    <div class="card login-option">
                        <a href="{{ route('microsoft.redirect') }}">
                            <div class="card-body d-flex align-items-center">
                                <img src="{{ asset('assets/svgs/microsift-icon.svg') }}" alt="Microsoft Icon"
                                    class="me-3" style="width: 24px; height: 24px;">
                                <p class="mb-0">Login with Microsoft</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
