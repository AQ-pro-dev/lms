<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['category_name' => 'MENTAL HEALTH'],
            ['category_name' => 'ETHICS'],
            ['category_name' => 'SUBSTANCE USE DISORDER'],
            // ['category_name' => 'Business'],
            // ['category_name' => 'Lifestyle'],
            // ['category_name' => 'Travel'],
        ];
        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
