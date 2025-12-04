<?php

namespace App\Livewire\Dashboard;

use App\Models\Category as ModelsCategory;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Category extends Component
{
    use LivewireAlert;

    public $category_name;
    public $category_list;
    public $search = '';
    public $categoryIdBeingDeleted;
    public $categoryIdBeingUpdated;
    public $updatedCategoryName;

    public function mount()
    {
        // Initially fetch all categories
        $this->category_list = ModelsCategory::all();
    }

    protected $rules = [
        'category_name' => 'required|string|max:30|unique:categories',
    ];

    public function createCategory()
    {
        $this->validate();

        ModelsCategory::create(['category_name' => $this->category_name]);

        $this->alert('success', 'Category created successfully.');

        $this->category_name = '';

        $this->category_list = ModelsCategory::all();
    }

    public function updatedSearch()
    {
        $this->category_list = ModelsCategory::where('category_name', 'like', '%' . $this->search . '%')->get();
    }

    public function confirmDelete($categoryId)
    {
        $this->categoryIdBeingDeleted = $categoryId;
    }

    public function destroyCategory()
    {
        ModelsCategory::find($this->categoryIdBeingDeleted)->delete();

        $this->alert('success', 'Category deleted successfully.');

        $this->category_list = ModelsCategory::all();

        $this->reset('categoryIdBeingDeleted');

        $this->dispatch('close-modal');
    }

    public function editCategory($categoryId)
    {
        $category = ModelsCategory::find($categoryId);
        $this->categoryIdBeingUpdated = $category->id;
        $this->updatedCategoryName = $category->category_name;
    }
    public function updateCategory()
    {
        $this->validate(['updatedCategoryName' => 'required|string|min:5|max:30|unique:categories,category_name,' . $this->categoryIdBeingUpdated]);
        $category = ModelsCategory::find($this->categoryIdBeingUpdated);
        $category->update(['category_name' => $this->updatedCategoryName]);

        $this->alert('success', 'Category updated successfully.');
        $this->category_list = ModelsCategory::all();
        $this->reset(['categoryIdBeingUpdated', 'updatedCategoryName']);

        $this->dispatch('close-modal');
    }
    public function closeModal()
    {
        $this->reset(['categoryIdBeingDeleted', 'categoryIdBeingUpdated', 'updatedCategoryName']);
    }

    public function render()
    {
        return view('livewire.dashboard.category')->layout('components.layouts.dashboard');
    }
}
