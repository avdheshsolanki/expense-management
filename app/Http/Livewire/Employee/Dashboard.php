<?php

namespace App\Http\Livewire\Employee;

use Livewire\Component;
use App\Models\Expense;
use Carbon\Carbon;

class Dashboard extends Component
{
    /**
     * Get current user's wallet balance
     */
    public function getWalletProperty()
    {
        return auth()->user()->wallet;
    }

    /**
     * Get recent expenses (last 10)
     */
    public function getRecentExpensesProperty()
    {
        return Expense::where('user_id', auth()->id())
            ->with('category')
            ->latest()
            ->take(10)
            ->get();
    }

    /**
     * Get current month's total expenses
     */
    public function getCurrentMonthTotalProperty()
    {
        return Expense::where('user_id', auth()->id())
            ->whereMonth('expense_date', Carbon::now()->month)
            ->whereYear('expense_date', Carbon::now()->year)
            ->sum('amount');
    }

    /**
     * Get spending by category for current month
     */
    public function getMonthlySpendingByCategoryProperty()
    {
        return Expense::where('user_id', auth()->id())
            ->whereMonth('expense_date', Carbon::now()->month)
            ->whereYear('expense_date', Carbon::now()->year)
            ->selectRaw('category_id, sum(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();
    }

    public function render()
    {
        return view('livewire.employee.dashboard')
            ->layout('layouts.app');
    }
}
