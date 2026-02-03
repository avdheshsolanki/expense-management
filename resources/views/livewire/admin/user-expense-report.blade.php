<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
                <!-- Header -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">User Expense Reports</h2>
                    <p class="text-gray-600 mt-1">View detailed expense reports for individual employees</p>
                </div>

                <!-- Flash Messages -->
                @if (session()->has('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif

                <!-- Filters Section -->
                <div class="bg-gray-50 p-4 rounded-lg mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- User Selection -->
                        <div>
                            <label for="selectedUserId" class="block text-sm font-medium text-gray-700 mb-2">
                                Select Employee <span class="text-red-500">*</span>
                            </label>
                            <select wire:model="selectedUserId"
                                    id="selectedUserId"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">-- Select Employee --</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}">{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Start Date -->
                        <div>
                            <label for="startDate" class="block text-sm font-medium text-gray-700 mb-2">
                                Start Date
                            </label>
                            <input type="date"
                                   wire:model="startDate"
                                   id="startDate"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <!-- End Date -->
                        <div>
                            <label for="endDate" class="block text-sm font-medium text-gray-700 mb-2">
                                End Date
                            </label>
                            <input type="date"
                                   wire:model="endDate"
                                   id="endDate"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label for="selectedCategory" class="block text-sm font-medium text-gray-700 mb-2">
                                Category
                            </label>
                            <select wire:model="selectedCategory"
                                    id="selectedCategory"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                <option value="">All Categories</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 flex gap-2">
                        <button wire:click="resetFilters"
                                class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm font-medium rounded-md transition duration-200">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                            Reset Filters
                        </button>
                        @if($selectedUserId && $expenses->count() > 0)
                            <button wire:click="exportCsv"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md transition duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Export to CSV
                            </button>
                        @endif
                    </div>
                </div>

                @if($selectedUser)
                    <!-- User Info & Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <!-- User Info Card -->
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-600">Employee</p>
                                    <p class="text-lg font-bold text-gray-900">{{ $selectedUser->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $selectedUser->email }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Total Expenses -->
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-600">Total Expenses</p>
                                    <p class="text-2xl font-bold text-gray-900">₹{{ number_format($this->stats['total_expenses'], 2) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Expense Count -->
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-10 h-10 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-600">Total Count</p>
                                    <p class="text-2xl font-bold text-gray-900">{{ $this->stats['expense_count'] }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Wallet Balance -->
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm font-medium text-gray-600">Current Balance</p>
                                    @php
                                        $balance = $selectedUser->wallet->balance ?? 0;
                                        $balanceClass = $balance < 0 ? 'text-red-600' : 'text-gray-900';
                                    @endphp
                                    <p class="text-2xl font-bold {{ $balanceClass }}">₹{{ number_format($balance, 2) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Charts Section -->
                    @if($expenses->count() > 0)
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                            <!-- Spending by Category Chart -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Spending by Category</h3>
                                <canvas id="categoryChart" class="max-h-64"></canvas>
                            </div>

                            <!-- Average Expense -->
                            <div class="bg-white border border-gray-200 rounded-lg p-6">
                                <h3 class="text-lg font-semibold text-gray-800 mb-4">Expense Statistics</h3>
                                <div class="space-y-4">
                                    <div class="flex justify-between items-center border-b pb-2">
                                        <span class="text-gray-600">Average Expense:</span>
                                        <span class="text-xl font-bold text-gray-900">₹{{ number_format($this->stats['average_expense'], 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center border-b pb-2">
                                        <span class="text-gray-600">Highest Expense:</span>
                                        <span class="text-xl font-bold text-gray-900">₹{{ number_format($expenses->max('amount'), 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center border-b pb-2">
                                        <span class="text-gray-600">Lowest Expense:</span>
                                        <span class="text-xl font-bold text-gray-900">₹{{ number_format($expenses->min('amount'), 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-600">Date Range:</span>
                                        <span class="text-sm text-gray-900">{{ $startDate }} to {{ $endDate }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Expenses Table -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Expense Details</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Category
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Description
                                        </th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Amount
                                        </th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($expenses as $expense)
                                        <tr class="hover:bg-gray-50 transition duration-200">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ Carbon\Carbon::parse($expense->expense_date)->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                    {{ $expense->category->name }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-600">
                                                {{ $expense->description ?: 'No description' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900">
                                                ₹{{ number_format($expense->amount, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if($expense->deleted_at)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        Deleted
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Active
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-12 h-12 text-gray-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    <p class="text-lg">No expenses found for the selected filters</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <!-- No User Selected State -->
                    <div class="text-center py-12">
                        <svg class="mx-auto h-24 w-24 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No Employee Selected</h3>
                        <p class="mt-2 text-sm text-gray-500">Please select an employee from the dropdown above to view their expense report.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Chart.js Script -->
    @if($selectedUser && $expenses->count() > 0)
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // Spending by Category Chart
                    const categoryCtx = document.getElementById('categoryChart');
                    if (categoryCtx) {
                        const categoryData = @json($this->spendingByCategory);

                        new Chart(categoryCtx, {
                            type: 'doughnut',
                            data: {
                                labels: categoryData.map(item => item.category.name),
                                datasets: [{
                                    data: categoryData.map(item => item.total),
                                    backgroundColor: [
                                        '#3B82F6', '#10B981', '#F59E0B', '#EF4444',
                                        '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'
                                    ],
                                    borderWidth: 2,
                                    borderColor: '#fff'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.label || '';
                                                if (label) {
                                                    label += ': ';
                                                }
                                                label += '₹' + context.parsed.toFixed(2);
                                                return label;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                });

                // Re-render charts on Livewire updates
                Livewire.hook('message.processed', (message, component) => {
                    const categoryCtx = document.getElementById('categoryChart');
                    if (categoryCtx) {
                        const categoryData = @json($this->spendingByCategory);

                        // Destroy existing chart if it exists
                        if (window.categoryChartInstance) {
                            window.categoryChartInstance.destroy();
                        }

                        window.categoryChartInstance = new Chart(categoryCtx, {
                            type: 'doughnut',
                            data: {
                                labels: categoryData.map(item => item.category.name),
                                datasets: [{
                                    data: categoryData.map(item => item.total),
                                    backgroundColor: [
                                        '#3B82F6', '#10B981', '#F59E0B', '#EF4444',
                                        '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'
                                    ],
                                    borderWidth: 2,
                                    borderColor: '#fff'
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        position: 'bottom',
                                    },
                                    tooltip: {
                                        callbacks: {
                                            label: function(context) {
                                                let label = context.label || '';
                                                if (label) {
                                                    label += ': ';
                                                }
                                                label += '₹' + context.parsed.toFixed(2);
                                                return label;
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                });
            </script>
        @endpush
    @endif
</div>
