<?php

namespace App\Http\Livewire\Employee;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Expense;
use App\Models\Category;
use Carbon\Carbon;

class Expenses extends Component
{
    use WithPagination;

    // Search and filter properties
    public $search = '';
    public $categoryFilter = '';
    public $dateFrom = '';
    public $dateTo = '';
    public $amountMin = '';
    public $amountMax = '';

    // Delete modal properties
    public $showDeleteModal = false;
    public $expenseToDelete = null;

    protected $paginationTheme = 'tailwind';

    /**
     * Reset pagination when search/filter changes
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    /**
     * Get all categories for filter dropdown
     */
    public function getCategoriesProperty()
    {
        return Category::all();
    }

    /**
     * Export expenses as CSV
     */
    public function exportCSV()
    {
        $expenses = $this->getFilteredExpenses();

        $filename = 'expenses_' . Carbon::now()->format('Y-m-d_His') . '.csv';
        $filepath = storage_path('app/public/' . $filename);

        $file = fopen($filepath, 'w');

        // Add CSV headers
        fputcsv($file, ['Date', 'Category', 'Amount', 'Description']);

        // Add expense data
        foreach ($expenses as $expense) {
            fputcsv($file, [
                $expense->expense_date->format('Y-m-d'),
                $expense->category->name,
                $expense->amount,
                $expense->description,
            ]);
        }

        fclose($file);

        return response()->download($filepath, $filename)->deleteFileAfterSend();
    }

    /**
     * Get filtered expenses without pagination (for export)
     */
    private function getFilteredExpenses()
    {
        $query = Expense::where('user_id', auth()->id())
            ->with('category');

        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%');
        }

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        if ($this->dateFrom) {
            $query->where('expense_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('expense_date', '<=', $this->dateTo);
        }

        if ($this->amountMin) {
            $query->where('amount', '>=', $this->amountMin);
        }

        if ($this->amountMax) {
            $query->where('amount', '<=', $this->amountMax);
        }

        return $query->latest()->get();
    }

    /**
     * Show delete confirmation modal
     */
    public function confirmDelete($id)
    {
        $this->expenseToDelete = $id;
        $this->showDeleteModal = true;
    }

    /**
     * Close delete confirmation modal
     */
    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->expenseToDelete = null;
    }

    /**
     * Delete an expense
     */
    public function delete()
    {
        $id = $this->expenseToDelete;
        $expense = Expense::where('user_id', auth()->id())->findOrFail($id);

        // Refund the amount back to wallet
        $wallet = auth()->user()->wallet;
        if ($wallet) {
            $wallet->addFunds($expense->amount, "Refund for deleted expense: {$expense->category->name}");
        }

        $expense->delete();

        $this->closeDeleteModal();

        session()->flash('success', 'Expense deleted successfully and amount refunded to wallet!');
    }

    /**
     * Restore a soft-deleted expense
     */
    public function restore($id)
    {
        $expense = Expense::withTrashed()
            ->where('user_id', auth()->id())
            ->findOrFail($id);

        // Deduct from wallet again
        $wallet = auth()->user()->wallet;
        if ($wallet) {
            $wallet->deductFunds($expense->amount, "Restored expense: {$expense->category->name}", $expense->id);
        }

        $expense->restore();

        session()->flash('success', 'Expense restored successfully!');
    }

    /**
     * Reset all filters
     */
    public function resetFilters()
    {
        $this->search = '';
        $this->categoryFilter = '';
        $this->dateFrom = '';
        $this->dateTo = '';
        $this->amountMin = '';
        $this->amountMax = '';
    }

    public function render()
    {
        $query = Expense::where('user_id', auth()->id())
            ->with('category');

        // Apply filters
        if ($this->search) {
            $query->where('description', 'like', '%' . $this->search . '%');
        }

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        if ($this->dateFrom) {
            $query->where('expense_date', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->where('expense_date', '<=', $this->dateTo);
        }

        if ($this->amountMin) {
            $query->where('amount', '>=', $this->amountMin);
        }

        if ($this->amountMax) {
            $query->where('amount', '<=', $this->amountMax);
        }

        $expenses = $query->latest()->paginate(10);

        return view('livewire.employee.expenses', [
            'expenses' => $expenses,
            'categories' => Category::all(),
        ])->layout('layouts.app');
    }
}
