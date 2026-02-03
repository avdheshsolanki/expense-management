<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create expense categories
        $categories = [
            [
                'name' => 'Travel',
                'description' => 'Business travel expenses including flights, hotels, and transportation',
            ],
            [
                'name' => 'Food & Dining',
                'description' => 'Meals, snacks, and dining expenses',
            ],
            [
                'name' => 'Office Supplies',
                'description' => 'Stationery, pens, notebooks, and other office supplies',
            ],
            [
                'name' => 'Software & Tools',
                'description' => 'Software subscriptions, licenses, and development tools',
            ],
            [
                'name' => 'Hardware',
                'description' => 'Computer equipment, peripherals, and hardware',
            ],
            [
                'name' => 'Training & Education',
                'description' => 'Courses, certifications, and training programs',
            ],
            [
                'name' => 'Utilities',
                'description' => 'Internet, electricity, and other utilities',
            ],
            [
                'name' => 'Miscellaneous',
                'description' => 'Other expenses not covered by other categories',
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
