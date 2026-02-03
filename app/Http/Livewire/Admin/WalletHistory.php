<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WalletTransaction;
use App\Models\User;
use Carbon\Carbon;

class WalletHistory extends Component
{
    use WithPagination;

    public $selectedUser = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $transactionType = '';

    protected $paginationTheme = 'tailwind';

    /**
     * Reset pagination when filters change
     */
    public function updatingSelectedUser()
    {
        $this->resetPage();
    }

    public function updatingDateFrom()
    {
        $this->resetPage();
    }

    public function updatingDateTo()
    {
        $this->resetPage();
    }

    public function updatingTransactionType()
    {
        $this->resetPage();
    }

    /**
     * Reset all filters
     */
    public function resetFilters()
    {
        $this->selectedUser = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->transactionType = '';
    }

    /**
     * Export transactions as CSV
     */
    public function exportCSV()
    {
        $transactions = $this->getFilteredTransactions();

        $filename = 'wallet_history_all_' . Carbon::now()->format('Y-m-d_His') . '.csv';
        $filepath = storage_path('app/public/' . $filename);

        $file = fopen($filepath, 'w');

        // Add CSV headers
        fputcsv($file, ['Date', 'User', 'Type', 'Amount', 'Balance After', 'Description']);

        // Add transaction data
        foreach ($transactions as $transaction) {
            fputcsv($file, [
                $transaction->created_at->format('Y-m-d H:i:s'),
                $transaction->wallet->user->name,
                ucfirst($transaction->type),
                '₹' . number_format($transaction->amount, 2),
                '₹' . number_format($transaction->balance_after, 2),
                $transaction->description,
            ]);
        }

        fclose($file);

        return response()->download($filepath, $filename)->deleteFileAfterSend();
    }

    /**
     * Get filtered transactions without pagination (for export)
     */
    private function getFilteredTransactions()
    {
        $query = WalletTransaction::with(['wallet.user']);

        if ($this->selectedUser) {
            $query->whereHas('wallet', function($q) {
                $q->where('user_id', $this->selectedUser);
            });
        }

        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }

        if ($this->transactionType) {
            $query->where('type', $this->transactionType);
        }

        return $query->latest()->get();
    }

    /**
     * Get statistics based on filters
     */
    public function getStatsProperty()
    {
        $query = WalletTransaction::query();

        if ($this->selectedUser) {
            $query->whereHas('wallet', function($q) {
                $q->where('user_id', $this->selectedUser);
            });
        }

        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }

        if ($this->transactionType) {
            $query->where('type', $this->transactionType);
        }

        $totalCredits = (clone $query)->where('type', 'credit')->sum('amount');
        $totalDebits = (clone $query)->where('type', 'debit')->sum('amount');
        $transactionCount = $query->count();

        return [
            'total_credits' => $totalCredits,
            'total_debits' => $totalDebits,
            'transaction_count' => $transactionCount,
        ];
    }

    public function render()
    {
        $query = WalletTransaction::with(['wallet.user']);

        // Apply filters
        if ($this->selectedUser) {
            $query->whereHas('wallet', function($q) {
                $q->where('user_id', $this->selectedUser);
            });
        }

        if ($this->dateFrom) {
            $query->where('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('created_at', '<=', $this->dateTo . ' 23:59:59');
        }

        if ($this->transactionType) {
            $query->where('type', $this->transactionType);
        }

        $transactions = $query->latest()->paginate(15);
        $users = User::orderBy('name')->get();

        return view('livewire.admin.wallet-history', [
            'transactions' => $transactions,
            'users' => $users,
        ])->layout('layouts.app');
    }
}
