<div class="help-request px-4">
     @push('title')
        Help Request
    @endpush
    <section class="help-request-alert py-5 px-4 mt-3">
        <div class="row align-items-center justify-content-center flex-wrap">
            <div class="col-md-7">
                <div class="help-request-content d-flex align-items-center justify-content-center gap-3">
                    <div class="alert-icon">
                        <i class="fa-solid fa-circle-exclamation fa-2xl" style="color: #000000;"></i>
                    </div>
                    <div class="alert-text">
                        <p>
                            Do not share sensitive information (attachments or text). Ex. Your credit card details or
                            personal ID numbers.
                        </p>
                    </div>
                </div>
                <form wire:submit.prevent="createRequest" enctype="multipart/form-data" class="mt-5 helprequest-form">
                    <label for="subject" class="mt-4 mb-3">Subject</label>
                    <input type="text" class="form-control" id="subject" wire:model="subject" name="subject"
                        placeholder="e.g I have an issue in my course video">
                    @error('subject')
                        <li class="text-danger">{{ $message }}</li>
                    @enderror
                    <label for="Description" class="my-3">Description</label>
                    <textarea name="request_detail" wire:model="request_detail" class="form-control" id="Description" cols="30"
                        rows="10"></textarea>
                    @error('request_detail')
                        <li class="text-danger">{{ $message }}</li>
                    @enderror
                    <div class="custom-file-input">
                        <label for="file-upload" class="custom-file-upload button-secondary mt-3">
                            Attach File
                        </label>
                        <input id="file-upload" wire:model="request_img" type="file" />
                        @error('request_img')
                            <li class="text-danger">{{ $message }}</li>
                        @enderror
                    </div>
                    <div class="text-center">
                        <button type="submit" class="button-primary mt-4 ms-5" wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="createRequest">Send Support Request</span>
                            <span wire:loading wire:target="createRequest">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                Sending...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
            <div class="col-md-5">
                <div class="help-img">
                    <img class="w-100" src="{{ asset('assets/images/help-img.webp') }}" alt="Help Image">
                </div>
            </div>
        </div>
    </section>

    <section class="help-request-review">
        <div class="help-request-head py-4 px-4 my-5">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="d-flex align-items-center gap-3">
                        <div class="request-icon d-flex align-items-center justify-content-center">
                            <i class="fa-solid fa-bookmark" style="color: #156636;"></i>
                        </div>
                        <div class="request-text">
                            <span class="">Support</span>
                            <h4 class="mb-0">List of Support Request Submissions</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="input-group d-flex align-items-center justify-content-end gap-2 position-relative">
                        <input type="text" class="form-control" placeholder="Search">
                        <i class="fa-solid fa-magnifying-glass position-absolute"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="help-request-body mb-5">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Subject</th>
                            <th scope="col">Created</th>
                            <th scope="col">Last Activity</th>
                            <th scope="col">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $key => $request)
                            <tr>
                                <td scope="row">{{ $key + 1 }}</td>
                                <td>{{ $request->subject }}</td>
                                <td>{{ $request->created_at->format('Y-m-d H:i') }}</td>
                                <td>{{ $request->updated_at->diffForHumans() }}</td>
                                <td>{{ $request->status ?? 'Pending' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No support requests found. Please submit a request if you need assistance.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>
