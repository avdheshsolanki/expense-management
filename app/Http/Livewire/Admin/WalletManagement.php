<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Wallet;

class WalletManagement extends Component
{
    public $selectedEmployee;
    public $amount;
    public $description;
    public $transactionType = 'credit'; // 'credit' or 'debit'
    public $showModal = false;

    protected $rules = [
        'selectedEmployee' => 'required|exists:users,id',
        'amount' => 'required|numeric|min:0.01',
        'description' => 'nullable|max:255',
    ];

    /**
     * Open modal for adding funds
     */
    public function openAddFundsModal($employeeId)
    {
        $this->resetForm();
        $this->selectedEmployee = $employeeId;
        $this->transactionType = 'credit';
        $this->showModal = true;
    }

    /**
     * Open modal for adjusting balance (debit)
     */
    public function openAdjustBalanceModal($employeeId)
    {
        $this->resetForm();
        $this->selectedEmployee = $employeeId;
        $this->transactionType = 'debit';
        $this->showModal = true;
    }

    /**
     * Process the transaction (add or deduct funds)
     */
    public function processTransaction()
    {
        $this->validate();

        $user = User::findOrFail($this->selectedEmployee);
        $wallet = $user->wallet;

        if (!$wallet) {
            // Create wallet if it doesn't exist
            $wallet = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0.00,
            ]);
        }

        $description = $this->description ?: ($this->transactionType === 'credit' ? 'Funds added by admin' : 'Balance adjusted by admin');

        if ($this->transactionType === 'credit') {
            $wallet->addFunds($this->amount, $description);
            session()->flash('success', 'Funds added successfully!');
        } else {
            $wallet->deductFunds($this->amount, $description);
            session()->flash('success', 'Balance adjusted successfully!');
        }

        $this->closeModal();
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
        $this->selectedEmployee = null;
        $this->amount = '';
        $this->description = '';
        $this->resetValidation();
    }

    public function render()
    {
        $employees = User::where('role', 'employee')
            ->with('wallet')
            ->latest()
            ->get();

        return view('livewire.admin.wallet-management', [
            'employees' => $employees,
        ])->layout('layouts.app');
    }
}
