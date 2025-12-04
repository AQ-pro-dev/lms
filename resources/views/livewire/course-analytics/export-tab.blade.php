<div class="export px-4">
    <section class="export-content px-4 py-3">
        <div class="row align-items-center justify-content-around flex-wrap">
            <div class="col-md-6">
                <h4 class="mt-2">Detailed Report of your Sales & Students</h4>
                <p class="mb-4">Export to keep a copy of your analytics data.</p>
                <button wire:click="exportToCSV" class="button-primary">Download CSV</button>
            </div>
            <div class="col-md-6 text-center">
                <div class="export-img">
                    <img src="{{ asset('assets/images/Export-cvs-img.webp') }}" alt="Export Image">
                </div>
            </div>
        </div>
    </section>
</div>
