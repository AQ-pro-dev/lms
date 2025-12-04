<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'Featured'],
            ['name' => 'New'],
            ['name' => 'Most Popular'],
            ['name' => 'Trending'],
            ['name' => 'Top Rated'],
            ['name' => 'Editor\'s Choice'],
            ['name' => 'Best Seller'],
        ];
        foreach ($tags as $tag) {
            Tag::create($tag);
        }
    }
}
