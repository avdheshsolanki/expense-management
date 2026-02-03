<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Expense;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create Admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@expense.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create wallet for admin
        Wallet::create([
            'user_id' => $admin->id,
            'balance' => 0.00,
        ]);

        // Create Employee users
        $employees = [
            [
                'name' => 'John Doe',
                'email' => 'john@expense.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
            ],
            [
                'name' => 'Jane Smith',
                'email' => 'jane@expense.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
            ],
            [
                'name' => 'Bob Johnson',
                'email' => 'bob@expense.com',
                'password' => Hash::make('password'),
                'role' => 'employee',
            ],
        ];

        foreach ($employees as $employeeData) {
            $employee = User::create($employeeData);

            // Create wallet with initial balance for each employee
            $wallet = Wallet::create([
                'user_id' => $employee->id,
                'balance' => 5000.00,
            ]);

            // Add initial wallet transaction
            $wallet->transactions()->create([
                'type' => 'credit',
                'amount' => 5000.00,
                'balance_after' => 5000.00,
                'description' => 'Initial wallet balance',
            ]);

            // Create some demo expenses for each employee
            $this->createDemoExpenses($employee);
        }
    }

    /**
     * Create demo expenses for a user
     */
    private function createDemoExpenses($user)
    {
        $categories = Category::all();

        if ($categories->isEmpty()) {
            return;
        }

        // Create 5-8 random expenses for each user
        $expenseCount = rand(5, 8);

        for ($i = 0; $i < $expenseCount; $i++) {
            $amount = rand(50, 500);
            $category = $categories->random();

            Expense::create([
                'user_id' => $user->id,
                'category_id' => $category->id,
                'amount' => $amount,
                'description' => $this->getRandomDescription($category->name),
                'expense_date' => now()->subDays(rand(1, 30)),
            ]);
        }
    }

    /**
     * Get a random expense description based on category
     */
    private function getRandomDescription($categoryName)
    {
        $descriptions = [
            'Travel' => [
                'Flight to client meeting',
                'Hotel accommodation for conference',
                'Taxi to airport',
                'Train ticket for business trip',
            ],
            'Food & Dining' => [
                'Team lunch',
                'Client dinner meeting',
                'Coffee with colleagues',
                'Lunch during conference',
            ],
            'Office Supplies' => [
                'Notebooks and pens',
                'Printer paper',
                'Desk organizer',
                'Whiteboard markers',
            ],
            'Software & Tools' => [
                'IDE subscription',
                'Cloud storage',
                'Project management tool',
                'Design software license',
            ],
            'Hardware' => [
                'Wireless mouse',
                'External monitor',
                'USB-C hub',
                'Keyboard replacement',
            ],
            'Training & Education' => [
                'Online course subscription',
                'Technical certification',
                'Workshop registration',
                'Professional development book',
            ],
            'Utilities' => [
                'Internet bill',
                'Mobile data plan',
                'Office electricity',
            ],
            'Miscellaneous' => [
                'Parking fee',
                'Office cleaning',
                'Team building activity',
                'Gift for client',
            ],
        ];

        $categoryDescriptions = $descriptions[$categoryName] ?? ['Miscellaneous expense'];

        return $categoryDescriptions[array_rand($categoryDescriptions)];
    }
}
