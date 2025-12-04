<div class="tag-management px-4 py-5">
    @push('title')
        Tag Management
    @endpush
    <!-- Create Tag Section -->
    <section class="create-tag">
        <form wire:submit.prevent="createTag">
            <div class="row align-items-center">
                <div class="col-md-8 mt-3">
                    <input type="text" wire:model="name" placeholder="Create tag" class="form-control" />
                    @error('name')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="col-md-4 mt-3">
                    <input type="submit" value="Create" class="button-primary w-100" />
                </div>
            </div>
        </form>
    </section>

    <!-- Search Tag Section -->
    <section class="tag-search mt-5 position-relative">
        <input type="text" wire:model.live="search" placeholder="Search tags" class="form-control w-100" />
        <i class="fa-solid fa-magnifying-glass position-absolute"></i>
    </section>

    <!-- Tag List Section -->
    <section class="tag-list mt-5">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tag Name</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tags as $tag)
                        <tr>
                            <td>{{ $tag->id }}</td>
                            <td>{{ $tag->name }}</td>
                            <td>
                                <a href="#" wire:click.prevent="confirmUpdate({{ $tag->id }})"
                                    data-bs-toggle="modal" data-bs-target="#updateTagModal">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                            <td>
                                <a href="#" wire:click.prevent="confirmDelete({{ $tag->id }})"
                                    data-bs-toggle="modal" data-bs-target="#deleteTagModal">
                                    <i class="fas fa-trash text-danger"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    <!-- Update Modal -->
    <div wire:ignore.self class="modal fade" id="updateTagModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form wire:submit.prevent="updateTag">
                    <div class="modal-header">
                        <h5 class="modal-title text-white">Update Tag</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"
                            data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="text" wire:model="name" class="form-control" />
                        @error('name')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="button-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div wire:ignore.self class="modal fade" id="deleteTagModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white">Delete Tag</h5>
                    <button type="button" class="btn-close" wire:click="closeModal" data-bs-dismiss="modal"></button>
                </div>
                <form wire:submit.prevent="destroyTag">
                    <div class="modal-body">
                        Are you sure you want to delete this tag?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-bs-dismiss="modal">Close</button>
                        <button data-bs-dismiss="modal" type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('js')
    <script>
        window.addEventListener('close-modal', event => {
            $('#deleteTagModal').modal('hide');
            $('#updateTagModal').modal('hide');
        });
    </script>
@endpush
