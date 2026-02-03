<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Support\Facades\Hash;

class UserManagement extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role = 'employee';
    public $initial_balance = 0;
    public $userId;
    public $isEditing = false;
    public $showModal = false;
    public $showDeleteModal = false;
    public $userToDelete;

    protected $rules = [
        'name' => 'required|min:3|max:255',
        'email' => 'required|email|max:255',
        'role' => 'required|in:admin,employee',
        'initial_balance' => 'nullable|numeric|min:0',
    ];

    /**
     * Open modal for creating a new user
     */
    public function openModal()
    {
        $this->resetForm();
        $this->showModal = true;
        $this->isEditing = false;
    }

    /**
     * Open modal for editing a user
     */
    public function edit($id)
    {
        $user = User::with('wallet')->findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->role = $user->role;
        $this->initial_balance = $user->wallet->balance ?? 0;
        $this->isEditing = true;
        $this->showModal = true;
    }

    /**
     * Save user (create or update)
     */
    public function save()
    {
        // Add password validation rules for new users
        if (!$this->isEditing) {
            $this->validate([
                'name' => 'required|min:3|max:255',
                'email' => 'required|email|max:255|unique:users,email',
                'password' => 'required|min:8|confirmed',
                'role' => 'required|in:admin,employee',
                'initial_balance' => 'nullable|numeric|min:0',
            ]);
        } else {
            $this->validate([
                'name' => 'required|min:3|max:255',
                'email' => 'required|email|max:255|unique:users,email,' . $this->userId,
                'role' => 'required|in:admin,employee',
            ]);

            // If password is provided, validate it
            if (!empty($this->password)) {
                $this->validate([
                    'password' => 'min:8|confirmed',
                ]);
            }
        }

        if ($this->isEditing) {
            // Update existing user
            $user = User::findOrFail($this->userId);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'role' => $this->role,
                'password' => !empty($this->password) ? Hash::make($this->password) : $user->password,
            ]);

            session()->flash('success', 'User updated successfully!');
        } else {
            // Create new user
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'role' => $this->role,
            ]);

            // Create wallet for the user
            Wallet::create([
                'user_id' => $user->id,
                'balance' => $this->initial_balance ?? 0,
            ]);

            // If initial balance > 0, create a transaction record
            if ($this->initial_balance > 0) {
                $user->wallet->transactions()->create([
                    'type' => 'credit',
                    'amount' => $this->initial_balance,
                    'balance_after' => $this->initial_balance,
                    'description' => 'Initial wallet balance',
                ]);
            }

            session()->flash('success', 'User created successfully!');
        }

        $this->closeModal();
    }

    /**
     * Show delete confirmation modal
     */
    public function confirmDelete($id)
    {
        $this->userToDelete = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Delete a user
     */
    public function delete()
    {
        try {
            $user = User::findOrFail($this->userToDelete);

            // Prevent deleting the current logged-in user
            if ($user->id === auth()->id()) {
                session()->flash('error', 'You cannot delete your own account!');
                $this->closeDeleteModal();
                return;
            }

            $user->delete();
            session()->flash('success', 'User deleted successfully!');
        } catch (\Exception $e) {
            session()->flash('error', 'Cannot delete user. Error: ' . $e->getMessage());
        }

        $this->closeDeleteModal();
    }

    /**
     * Close delete confirmation modal
     */
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->userToDelete = null;
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
        $this->email = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role = 'employee';
        $this->initial_balance = 0;
        $this->userId = null;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.user-management', [
            'users' => User::with('wallet')->latest()->get(),
        ])->layout('layouts.app');
    }
}
