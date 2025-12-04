<div class="email py-5 px-4">.
    @push('title')
        Emails
    @endpush
    <h3>GUEST EMAILS</h3>

    <!-- Table to display emails -->
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Email</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($emails as $email)
                    <tr>
                        <td>{{ $email->id }}</td>
                        <td>{{ $email->email }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Export Button -->
    <div class="d-flex justify-content-end mt-4">
        <!-- Check if exporting is in progress -->
        <button wire:click="exportCsv" class="button-primary" wire:loading.attr="disabled">
            <!-- Show spinner when loading -->
            <span wire:loading.remove>Export as CSV</span>
            <span wire:loading>
                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                Exporting...
            </span>
        </button>
    </div>
</div>
