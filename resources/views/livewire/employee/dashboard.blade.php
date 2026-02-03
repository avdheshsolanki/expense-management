<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Employee Dashboard</h2>
            <p class="text-gray-600">Welcome back, {{ auth()->user()->name }}!</p>
        </div>

        <!-- Wallet Balance Card -->
        <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg shadow-lg p-6 mb-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <p class="text-sm opacity-90">Current Wallet Balance</p>
                    <p class="text-4xl font-bold mt-2">₹{{ number_format($this->wallet->balance ?? 0, 2) }}</p>
                    <p class="text-sm mt-2 opacity-75">
                        @if($this->wallet && $this->wallet->balance < 0)
                            <span class="bg-red-500 px-2 py-1 rounded">Negative Balance</span>
                        @elseif($this->wallet && $this->wallet->balance < 500)
                            <span class="bg-yellow-500 px-2 py-1 rounded">Low Balance</span>
                        @else
                            <span class="bg-green-500 px-2 py-1 rounded">Good Balance</span>
                        @endif
                    </p>
                </div>
                <div class="bg-white bg-opacity-20 p-4 rounded-full">
                    <svg class="h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Current Month Total -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">This Month's Expenses</p>
                        <p class="text-2xl font-bold text-gray-900">₹{{ number_format($this->currentMonthTotal, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Total Expenses -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                        <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Expenses</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $this->recentExpenses->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart and Recent Expenses Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Monthly Spending Chart -->
            @if($this->monthlySpendingByCategory->isNotEmpty())
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">This Month's Spending by Category</h3>
                    <canvas id="employeeChart"></canvas>
                </div>
            @endif

            <!-- Recent Expenses -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-800">Recent Expenses</h3>
                    <a href="{{ route('employee.expenses') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All</a>
                </div>
                <div class="space-y-3">
                    @forelse($this->recentExpenses->take(5) as $expense)
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">{{ $expense->category->name }}</p>
                                <p class="text-xs text-gray-500">{{ $expense->expense_date->format('M d, Y') }}</p>
                                @if($expense->description)
                                    <p class="text-xs text-gray-600 mt-1">{{ Str::limit($expense->description, 40) }}</p>
                                @endif
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-bold text-gray-900">₹{{ number_format($expense->amount, 2) }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">No expenses yet. Start tracking your expenses!</p>
                    @endforelse
                </div>
                @if($this->recentExpenses->count() > 5)
                    <div class="mt-4 text-center">
                        <a href="{{ route('employee.expenses') }}" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            View {{ $this->recentExpenses->count() - 5 }} more →
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <a href="{{ route('employee.add-expense') }}" class="flex items-center justify-center p-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition shadow-sm">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Add New Expense
                </a>
                <a href="{{ route('employee.expenses') }}" class="flex items-center justify-center p-4 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition shadow-sm">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    View All Expenses
                </a>
                <a href="{{ route('employee.expenses') }}?export=csv" class="flex items-center justify-center p-4 bg-green-600 hover:bg-green-700 text-white rounded-lg transition shadow-sm">
                    <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export Report
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('employeeChart');
        if (ctx) {
            const data = {
                labels: @json($this->monthlySpendingByCategory->pluck('category.name')),
                datasets: [{
                    label: 'Amount ($)',
                    data: @json($this->monthlySpendingByCategory->pluck('total')),
                    backgroundColor: [
                        'rgba(99, 102, 241, 0.7)',
                        'rgba(239, 68, 68, 0.7)',
                        'rgba(34, 197, 94, 0.7)',
                        'rgba(234, 179, 8, 0.7)',
                        'rgba(168, 85, 247, 0.7)',
                        'rgba(236, 72, 153, 0.7)',
                        'rgba(14, 165, 233, 0.7)',
                        'rgba(251, 146, 60, 0.7)',
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            };

            new Chart(ctx, {
                type: 'doughnut',
                data: data,
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
        }
    });
</script>
@endpush
