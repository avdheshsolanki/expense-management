<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\WalletTransaction;
use App\Models\Expense;
use App\Models\Category;
use Carbon\Carbon;

class UserDetail extends Component
{
    use WithPagination;

    public $userId;
    public $user;
    public $dateFrom;
    public $dateTo;

    protected $paginationTheme = 'tailwind';

    public function mount($id)
    {
        $this->userId = $id;
        $this->user = User::with('wallet')->findOrFail($id);

        // Default to current month
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    /**
     * Reset pagination when filters change
     */
    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    /**
     * Get wallet statistics
     */
    public function getWalletStatsProperty()
    {
        if (!$this->user->wallet) {
            return [
                'current_balance' => 0,
                'total_credits' => 0,
                'total_debits' => 0,
                'transaction_count' => 0,
            ];
        }

        $query = WalletTransaction::where('wallet_id', $this->user->wallet->id);

        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }

        $totalCredits = (clone $query)->where('type', 'credit')->sum('amount');
        $totalDebits = (clone $query)->where('type', 'debit')->sum('amount');
        $transactionCount = $query->count();

        return [
            'current_balance' => $this->user->wallet->balance,
            'total_credits' => $totalCredits,
            'total_debits' => $totalDebits,
            'transaction_count' => $transactionCount,
        ];
    }

    /**
     * Get expense statistics
     */
    public function getExpenseStatsProperty()
    {
        $query = Expense::where('user_id', $this->userId);

        if ($this->dateFrom) {
            $query->where('expense_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('expense_date', '<=', $this->dateTo);
        }

        $totalExpenses = $query->sum('amount');
        $expenseCount = $query->count();
        $averageExpense = $expenseCount > 0 ? $totalExpenses / $expenseCount : 0;

        // Get expenses by category
        $expensesByCategory = Expense::where('user_id', $this->userId)
            ->when($this->dateFrom, function($q) {
                $q->where('expense_date', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($q) {
                $q->where('expense_date', '<=', $this->dateTo);
            })
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(function($expenses) {
                return [
                    'category' => $expenses->first()->category->name,
                    'total' => $expenses->sum('amount'),
                    'count' => $expenses->count(),
                ];
            })
            ->sortByDesc('total')
            ->values();

        return [
            'total_expenses' => $totalExpenses,
            'expense_count' => $expenseCount,
            'average_expense' => $averageExpense,
            'by_category' => $expensesByCategory,
        ];
    }

    /**
     * Get recent transactions
     */
    public function getRecentTransactionsProperty()
    {
        if (!$this->user->wallet) {
            return collect([]);
        }

        return WalletTransaction::where('wallet_id', $this->user->wallet->id)
            ->when($this->dateFrom, function($q) {
                $q->where('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($q) {
                $q->where('created_at', '<=', $this->dateTo . ' 23:59:59');
            })
            ->latest()
            ->paginate(10);
    }

    /**
     * Reset filters
     */
    public function resetFilters()
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function render()
    {
        return view('livewire.admin.user-detail')->layout('layouts.app');
    }
}
