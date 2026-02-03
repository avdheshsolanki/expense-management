<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Expense;
use App\Models\Category;
use Carbon\Carbon;

class Dashboard extends Component
{
    // Filter properties
    public $startDate;
    public $endDate;
    public $selectedCategory = '';

    public function mount()
    {
        // Set default date range to current month
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    /**
     * Get all employees with their wallet balances
     */
    public function getEmployeesProperty()
    {
        return User::where('role', 'employee')
            ->with('wallet')
            ->get();
    }

    /**
     * Get total monthly spending across all employees
     */
    public function getTotalMonthlySpendingProperty()
    {
        $query = Expense::query();

        if ($this->startDate) {
            $query->where('expense_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('expense_date', '<=', $this->endDate);
        }

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        return $query->sum('amount');
    }

    /**
     * Get spending by category
     */
    public function getSpendingByCategoryProperty()
    {
        $query = Expense::query()
            ->selectRaw('category_id, sum(amount) as total')
            ->groupBy('category_id')
            ->with('category');

        if ($this->startDate) {
            $query->where('expense_date', '>=', $this->startDate);
        }

        if ($this->endDate) {
            $query->where('expense_date', '<=', $this->endDate);
        }

        return $query->get();
    }

    /**
     * Get all categories for filter
     */
    public function getCategoriesProperty()
    {
        return Category::all();
    }

    /**
     * Reset filters
     */
    public function resetFilters()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->selectedCategory = '';
    }

    public function render()
    {
        return view('livewire.admin.dashboard', [
            'categories' => Category::all(),
        ])->layout('layouts.app');
    }
}
