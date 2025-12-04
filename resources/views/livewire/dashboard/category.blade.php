<div class="category px-4 py-5">
    @push('title')
        Category
    @endpush
    <section>
        <div class="create-category">
            <form wire:submit.prevent="createCategory">
                <div class="row align-items-center">
                    <div class="col-md-8 mt-3">
                        <input type="text" wire:model="category_name" placeholder="Create category"
                            class="form-control w-100">
                        @error('category_name')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="col-md-4 mt-3">
                        <input type="submit" value="Create" class="button-primary w-100">
                    </div>
                </div>
            </form>
        </div>
    </section>

    <!-- Search Input -->
    <section class="category-search mt-5">
        <div class="row align-items-center">
            <div class="col-md-8 position-relative">
                <input type="text" wire:model.live="search" placeholder="Search categories"
                    class="form-control w-100">
                <i class="fa-solid fa-magnifying-glass position-absolute"></i>
            </div>
        </div>
    </section>
    <section class="category-list mt-5">
        <div class="table-responsive">
            <table class="table table-borderless statements-table">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th>Category Name</th>
                        <th>Edit</th>
                        <th>Delete</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($category_list as $category)
                        <tr>
                            <td scope="col">{{ $category->id }}</td>
                            <td>{{ $category->category_name }}</td>
                            <td><a href="#" wire:click.prevent="editCategory({{ $category->id }})"
                                    data-bs-toggle="modal" data-bs-target="#editCategoryModal">
                                    <i class="fas fa-edit"></i>
                                </a></td>
                            <td>
                                <!-- Trigger delete modal and set category ID -->
                                <a href="#" wire:click.prevent="confirmDelete({{ $category->id }})"
                                    data-bs-toggle="modal" data-bs-target="#deleteCategoryModal">
                                    <i class="fas fa-trash text-danger"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
    <!-- Delete Confirmation Modal -->
    <div wire:ignore.self class="modal fade" id="deleteCategoryModal" tabindex="-1"
        aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="deleteCategoryModalLabel">Delete Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModal"
                        aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="destroyCategory">
                    <div class="modal-body">
                        Are you sure you want to delete this category ?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Yes, Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Edit Modal -->
    <div wire:ignore.self class="modal fade" id="editCategoryModal" tabindex="-1"
        aria-labelledby="editCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-white" id="editCategoryModalLabel">Edit Category</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" wire:click="closeModal"
                        aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="updateCategory">
                    <div class="modal-body">
                        <input type="text" wire:model="updatedCategoryName" class="form-control"
                            placeholder="Update category name">
                        @error('updatedCategoryName')
                            <span class="error">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="button-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@script
    <script>
        window.addEventListener('close-modal', event => {

            $('#deleteCategoryModal').modal('hide');
            $('#editCategoryModal').modal('hide');
        })
    </script>
@endscript
