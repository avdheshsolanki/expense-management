<?php

namespace App\Http\Livewire\Employee;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\WalletTransaction;
use Carbon\Carbon;

class TopupHistory extends Component
{
    use WithPagination;

    public $dateFrom = '';
    public $dateTo = '';
    public $transactionType = '';

    protected $paginationTheme = 'tailwind';

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

    public function updatingTransactionType()
    {
        $this->resetPage();
    }

    /**
     * Reset all filters
     */
    public function resetFilters()
    {
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

        $filename = 'wallet_history_' . Carbon::now()->format('Y-m-d_His') . '.csv';
        $filepath = storage_path('app/public/' . $filename);

        $file = fopen($filepath, 'w');

        // Add CSV headers
        fputcsv($file, ['Date', 'Type', 'Amount', 'Balance After', 'Description']);

        // Add transaction data
        foreach ($transactions as $transaction) {
            fputcsv($file, [
                $transaction->created_at->format('Y-m-d H:i:s'),
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
        $wallet = auth()->user()->wallet;

        if (!$wallet) {
            return collect([]);
        }

        $query = WalletTransaction::where('wallet_id', $wallet->id);

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

    public function render()
    {
        $wallet = auth()->user()->wallet;

        if (!$wallet) {
            $transactions = collect([]);
            $walletBalance = 0;
        } else {
            $query = WalletTransaction::where('wallet_id', $wallet->id);

            // Apply filters
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
            $walletBalance = $wallet->balance;
        }

        return view('livewire.employee.topup-history', [
            'transactions' => $transactions,
            'walletBalance' => $walletBalance,
        ])->layout('layouts.app');
    }
}
