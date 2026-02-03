<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Expense;
use App\Models\Category;
use Carbon\Carbon;

class UserExpenseReport extends Component
{
    public $selectedUserId;
    public $startDate;
    public $endDate;
    public $selectedCategory;

    protected $queryString = ['selectedUserId'];

    public function mount()
    {
        // Set default date range to current month
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    /**
     * Get filtered expenses for the selected user
     */
    private function getFilteredExpenses()
    {
        if (!$this->selectedUserId) {
            return collect([]);
        }

        $query = Expense::where('user_id', $this->selectedUserId)
            ->with(['category', 'user'])
            ->whereBetween('expense_date', [$this->startDate, $this->endDate]);

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        return $query->latest('expense_date')->get();
    }

    /**
     * Get expense statistics
     */
    public function getStatsProperty()
    {
        if (!$this->selectedUserId) {
            return [
                'total_expenses' => 0,
                'expense_count' => 0,
                'average_expense' => 0,
            ];
        }

        $expenses = $this->getFilteredExpenses();

        return [
            'total_expenses' => $expenses->sum('amount'),
            'expense_count' => $expenses->count(),
            'average_expense' => $expenses->count() > 0 ? $expenses->avg('amount') : 0,
        ];
    }

    /**
     * Get spending by category
     */
    public function getSpendingByCategoryProperty()
    {
        if (!$this->selectedUserId) {
            return collect([]);
        }

        return Expense::where('user_id', $this->selectedUserId)
            ->whereBetween('expense_date', [$this->startDate, $this->endDate])
            ->selectRaw('category_id, sum(amount) as total')
            ->groupBy('category_id')
            ->with('category')
            ->get();
    }

    /**
     * Export expenses to CSV
     */
    public function exportCsv()
    {
        if (!$this->selectedUserId) {
            session()->flash('error', 'Please select a user first.');
            return;
        }

        $expenses = $this->getFilteredExpenses();
        $user = User::find($this->selectedUserId);

        if ($expenses->isEmpty()) {
            session()->flash('error', 'No expenses to export.');
            return;
        }

        $filename = 'user_' . $user->id . '_expenses_' . Carbon::now()->format('Y-m-d_His') . '.csv';
        $filepath = storage_path('app/' . $filename);

        $file = fopen($filepath, 'w');
        fputcsv($file, ['Date', 'Category', 'Amount', 'Description', 'Status']);

        foreach ($expenses as $expense) {
            fputcsv($file, [
                $expense->expense_date,
                $expense->category->name,
                '₹' . number_format($expense->amount, 2),
                $expense->description,
                $expense->deleted_at ? 'Deleted' : 'Active',
            ]);
        }

        fclose($file);

        return response()->download($filepath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Reset filters
     */
    public function resetFilters()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->selectedCategory = null;
    }

    public function render()
    {
        $employees = User::where('role', 'employee')
            ->with('wallet')
            ->orderBy('name')
            ->get();

        $categories = Category::orderBy('name')->get();

        $selectedUser = $this->selectedUserId ? User::with('wallet')->find($this->selectedUserId) : null;
        $expenses = $this->getFilteredExpenses();

        return view('livewire.admin.user-expense-report', [
            'employees' => $employees,
            'categories' => $categories,
            'selectedUser' => $selectedUser,
            'expenses' => $expenses,
        ])->layout('layouts.app');
    }
}
