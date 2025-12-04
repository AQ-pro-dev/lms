<div class="help-requests-admin py-5 px-4">
    @push('title')
        Help Requests
    @endpush
    <h4 class="mb-4">Help Requests of Students</h4>
    <section class="help-requests">
        <div class="table-responsive">
            <table class="table table-borderless">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Student Name</th>
                        <th scope="col">Subject</th>
                        <th scope="col">Status</th>
                        <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($helprequests as $helprequest)
                        <tr>
                            <td scope="row">{{ $helprequest->id }}</td>
                            <td>{{ $helprequest->user->first_name }} {{ $helprequest->user->last_name }}</td>
                            <td>{{ $helprequest->subject }}</td>
                            <td>{{ ucfirst($helprequest->status) }}</td>
                            <td>
                                <button class="button-primary" data-bs-toggle="modal" data-bs-target="#detailsModal"
                                    wire:click="showDetails({{ $helprequest->id }})">
                                    Details
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title text-white fs-5" id="detailsModalLabel">
                        Request Details
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($selectedRequest)
                        <p><strong>Student Name:</strong> {{ $selectedRequest->user->first_name }}
                            {{ $selectedRequest->user->last_name }}</p>
                        <p><strong>Email:</strong> {{ $selectedRequest->user->email }}</p>
                        <p><strong>Subject:</strong> {{ $selectedRequest->subject }}</p>
                        <p><strong>Details:</strong> {{ $selectedRequest->request_detail }}</p>

                        @if ($selectedRequest->request_img)
                            <p>
                                <strong>File:</strong>
                                <a href="{{ asset('storage/' . $selectedRequest->request_img) }}" target="_blank">
                                    View File
                                </a>
                            </p>
                        @else
                            <p><strong>File:</strong> No file uploaded.</p>
                        @endif

                        @if ($selectedRequest->status === 'pending')
                            <div class="form-group">
                                <label for="response">Admin Response:</label>
                                <textarea wire:model="response" class="form-control" id="response" rows="4"></textarea>
                                @error('response')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    @else
                        <p>No request selected.</p>
                    @endif
                </div>
                <div class="modal-footer">
                    @if ($selectedRequest && $selectedRequest->status === 'pending')
                        <button type="button" class="btn btn-danger" wire:click="rejectRequest">Reject Request</button>
                        <button type="button" class="button-primary" wire:click="sendResponse">
                            Approve Request
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('close-modal', event => {
        $('#detailsModal').modal('hide');
    });
</script>
