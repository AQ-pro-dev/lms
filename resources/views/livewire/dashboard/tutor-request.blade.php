<div class="tutorRequest py-5 px-4">
    @push('title')
        Tutor Requests
    @endpush
    <h2 class="mb-4">Course Requests</h2>
    <section class="tag-list mt-5">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tutor Photo</th>
                        <th>Tutor Name</th>
                        <th>Specialization</th>
                        <th>Bio</th>
                        <th>Microsoft Account</th>
                        <th>Verified</th>
                        <th>CV</th>
                        <th>Approve</th>
                        <th>Reject</th>
                        <th>View</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tutors as $tutor)
                        <tr>
                            <td>{{ $tutor->id }}</td>
                            <td>
                                <img src="{{ $tutor->user->pfp ? asset('storage/' . $tutor->user->pfp) : asset('assets/images/dummy-profile-photo.webp') }}"
                                    alt="Tutor Photo" style="width: 50px; height: 50px; border-radius: 50%;">
                            </td>
                            <td>{{ $tutor->user->first_name . ' ' . $tutor->user->last_name }}</td>
                            <td>{{ $tutor->specialization }}</td>
                            <td>{{ $tutor->user->bio }}</td>
                            @if ($tutor->user->microsoft_account)
                                <td>True</td>
                            @else
                                <td>False</td>
                            @endif
                            <td>
                                <span
                                    class="badge text-white {{ $tutor->is_verified == 1 ? 'bg-success' : ($tutor->is_verified == 3 ? 'bg-danger' : 'bg-secondary') }}">
                                    {{ $tutor->is_verified == 1 ? 'Verified' : 'Unverified' }}
                                </span>
                            </td>
                            <td>
                                @if ($tutor->tutorFiles->isNotEmpty())
                                    <button wire:click="downloadFile('{{ $tutor->tutorFiles->first()->file_path }}')"
                                        class="btn btn-outline-primary">
                                        Download CV
                                    </button>
                                @else
                                    <span>No file uploaded</span>
                                @endif
                            </td>
                            <td>
                                <button class="btn btn-outline-success"
                                    wire:click="openApproveModal({{ $tutor->id }})" wire:loading.attr="disabled"
                                    wire:target="openApproveModal({{ $tutor->id }})"
                                    @if ($tutor->is_verified == 1) disabled @endif>
                                    <span wire:loading.remove wire:target="openApproveModal({{ $tutor->id }})">
                                        Approve
                                    </span>
                                    <span wire:loading wire:target="openApproveModal({{ $tutor->id }})">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                    </span>
                                </button>
                            </td>
                            <td>
                                <button class="btn btn-outline-danger"
                                    wire:click="openRejectModal({{ $tutor->id }})" wire:loading.attr="disabled"
                                    wire:target="openRejectModal({{ $tutor->id }})"
                                    @if ($tutor->is_verified == 3) disabled @endif>
                                    <span wire:loading.remove wire:target="openRejectModal({{ $tutor->id }})">
                                        Reject
                                    </span>
                                    <span wire:loading wire:target="openRejectModal({{ $tutor->id }})">
                                        <span class="spinner-border spinner-border-sm" role="status"
                                            aria-hidden="true"></span>
                                    </span>
                                </button>
                            </td>
                            <td>
                                <a href="{{ route('tutor.profile', $tutor->user_id) }}" target="_blank"
                                    class="btn btn-outline-warning">
                                    View Tutor
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <!-- Approve Modal -->
    <div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="approveModalLabel">Approve Tutor Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="submitApproval">
                        <div class="mb-3">
                            <label for="approvalMessage" class="form-label">Approval Message:</label>
                            <textarea class="form-control" id="approvalMessage" wire:model="approvalMessage" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">
                            Approve Tutor
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="rejectModalLabel">Reject Tutor Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form wire:submit.prevent="submitRejection">
                        <div class="mb-3">
                            <label for="rejectionReason" class="form-label">Rejection Reason:</label>
                            <textarea class="form-control" id="rejectionReason" wire:model="rejectionReason" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger">
                            Reject Tutor
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        window.addEventListener('showRejectModal', event => {
            $('#rejectModal').modal('show');
        });

        window.addEventListener('hideRejectModal', event => {
            $('#rejectModal').modal('hide');
        });

        window.addEventListener('showApproveModal', event => {
            $('#approveModal').modal('show');
        });

        window.addEventListener('hideApproveModal', event => {
            $('#approveModal').modal('hide');
        });
    </script>
@endpush
