<?php

namespace App\Http\Livewire\Employee;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Category;
use Carbon\Carbon;

class AddExpense extends Component
{
    public $amount;
    public $category_id;
    public $description;
    public $expense_date;

    protected $rules = [
        'amount' => 'required|numeric|min:0.01',
        'category_id' => 'required|exists:categories,id',
        'description' => 'nullable|max:500',
        'expense_date' => 'required|date|before_or_equal:today',
    ];

    protected $messages = [
        'expense_date.before_or_equal' => 'Expense date cannot be in the future.',
    ];

    public function mount()
    {
        // Set default expense date to today
        $this->expense_date = Carbon::now()->format('Y-m-d');
    }

    /**
     * Get all categories
     */
    public function getCategoriesProperty()
    {
        return Category::all();
    }

    /**
     * Get current wallet balance
     */
    public function getWalletBalanceProperty()
    {
        return auth()->user()->wallet->balance ?? 0;
    }

    /**
     * Save expense
     */
    public function save()
    {
        $this->validate();

        // Create expense (wallet will be automatically deducted via model event)
        Expense::create([
            'user_id' => auth()->id(),
            'category_id' => $this->category_id,
            'amount' => $this->amount,
            'description' => $this->description,
            'expense_date' => $this->expense_date,
        ]);

        session()->flash('message', 'Expense added successfully! Your wallet balance has been updated.');

        // Reset form
        $this->reset(['amount', 'category_id', 'description']);
        $this->expense_date = Carbon::now()->format('Y-m-d');

        // Redirect to expenses page
        return redirect()->route('employee.expenses');
    }

    public function render()
    {
        return view('livewire.employee.add-expense', [
            'categories' => Category::all(),
            'walletBalance' => auth()->user()->wallet->balance ?? 0,
        ])->layout('layouts.app');
    }
}
