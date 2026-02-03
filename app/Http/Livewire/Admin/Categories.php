<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\Category;

class Categories extends Component
{
    public $name;
    public $description;
    public $categoryId;
    public $isEditing = false;
    public $showModal = false;
    public $showDeleteModal = false;
    public $categoryToDelete;

    protected $rules = [
        'name' => 'required|min:3|max:255',
        'description' => 'nullable|max:500',
    ];

    /**
     * Open modal for creating a new category
     */
    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEditing = false;
    }

    /**
     * Open modal for editing a category
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->isEditing = true;
        $this->showModal = true;
    }

    /**
     * Save category (create or update)
     */
    public function save()
    {
        $this->validate();

        if ($this->isEditing) {
            $category = Category::findOrFail($this->categoryId);
            $category->update([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            session()->flash('success', 'Category updated successfully!');
        } else {
            Category::create([
                'name' => $this->name,
                'description' => $this->description,
            ]);
            session()->flash('success', 'Category created successfully!');
        }

        $this->closeModal();
    }

    /**
     * Show delete confirmation modal
     */
    public function confirmDelete($id)
    {
        $this->categoryToDelete = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Delete a category
     */
    public function delete()
    {
        try {
            $category = Category::findOrFail($this->categoryToDelete);
            $category->delete();
            session()->flash('success', 'Category deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Cannot delete category. It may be associated with expenses.');
        }

        $this->closeDeleteModal();
    }

    /**
     * Close delete confirmation modal
     */
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->categoryToDelete = null;
    }

    /**
     * Close modal and reset form
     */
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Reset form fields
     */
    private function resetForm()
    {
        $this->name = '';
        $this->description = '';
        $this->categoryId = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.categories', [
            'categories' => Category::latest()->get(),
        ])->layout('layouts.app');
    }
}
