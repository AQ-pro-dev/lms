<div class="system-settings py-5 px-4">
    @push('title')
        System Settings
    @endpush
    <div class="mt-4">
        <h4 class="mb-4 text-primary fw-bold">System Settings</h4>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-sliders me-2"></i>Pagination Settings</h5>
            </div>
            <div class="card-body">
                <form wire:submit.prevent="saveSettings">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Students Per Page</label>
                            <input type="number" class="form-control @error('studentsPerPage') is-invalid @enderror"
                                wire:model.defer="studentsPerPage" min="5" max="100" required>
                            @error('studentsPerPage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Number of students to display per page (5-100)</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Instructors Per Page</label>
                            <input type="number" class="form-control @error('instructorsPerPage') is-invalid @enderror"
                                wire:model.defer="instructorsPerPage" min="5" max="100" required>
                            @error('instructorsPerPage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Number of instructors to display per page (5-100)</small>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Admins Per Page</label>
                            <input type="number" class="form-control @error('adminsPerPage') is-invalid @enderror"
                                wire:model.defer="adminsPerPage" min="5" max="100" required>
                            @error('adminsPerPage')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Number of admins to display per page (5-100)</small>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <span wire:loading.remove wire:target="saveSettings">
                                <i class="bi bi-save me-2"></i>Save Settings
                            </span>
                            <span wire:loading wire:target="saveSettings">
                                <span class="spinner-border spinner-border-sm me-2"></span>Saving...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
