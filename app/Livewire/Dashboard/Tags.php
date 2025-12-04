<?php

namespace App\Livewire\Dashboard;

use App\Models\Tag;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class Tags extends Component
{
    use LivewireAlert;

    public $name;
    public $search = '';
    public $tagIdBeingDeleted = null;
    public $tagIdBeingUpdated = null;
    public $tags;

    protected $rules = [
        'name' => 'required|string|max:30|unique:tags,name',
    ];

    public function mount()
    {
        $this->refreshTagList();
    }

    public function refreshTagList()
    {
        $this->tags = Tag::where('name', 'like', '%' . $this->search . '%')->get();
    }

    public function createTag()
    {
        $this->validate();
        Tag::create(['name' => $this->name]);
        $this->alert('success', 'Tag created successfully.');
        $this->reset('name');
        $this->refreshTagList();
    }

    public function updatedSearch()
    {
        $this->refreshTagList();
    }

    public function confirmDelete($tagId)
    {
        $this->tagIdBeingDeleted = $tagId;
    }

    public function destroyTag()
    {
        Tag::findOrFail($this->tagIdBeingDeleted)->delete();
        $this->alert('success', 'Tag deleted successfully.');
        $this->reset('tagIdBeingDeleted');
        $this->refreshTagList();

        $this->dispatch('close-modal'); // Close modal after deletion
    }

    public function confirmUpdate($tagId)
    {
        $tag = Tag::findOrFail($tagId);
        $this->tagIdBeingUpdated = $tag->id;
        $this->name = $tag->name;
    }

    public function updateTag()
    {
        $this->validate([
            'name' => 'required|string|max:255|unique:tags,name,' . $this->tagIdBeingUpdated,
        ]);

        $tag = Tag::findOrFail($this->tagIdBeingUpdated);
        $tag->update(['name' => $this->name]);
        $this->alert('success', 'Tag updated successfully.');
        $this->reset(['tagIdBeingUpdated', 'name']);
        $this->refreshTagList();
        $this->dispatch('close-modal');
    }

    public function closeModal()
    {
        $this->reset(['tagIdBeingDeleted', 'tagIdBeingUpdated', 'name']);
    }

    public function render()
    {
        return view('livewire.dashboard.tags', [
            'tags' => Tag::where('name', 'like', '%' . $this->search . '%')->get(),
        ])->layout('components.layouts.dashboard');
    }
}
