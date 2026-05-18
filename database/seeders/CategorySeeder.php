<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            ['name' => 'Makanan & Minuman', 'icon' => '🍽️', 'color' => '#f97316'],
            ['name' => 'Belanja',           'icon' => '🛍️', 'color' => '#ec4899'],
            ['name' => 'Transportasi',      'icon' => '🚗', 'color' => '#3b82f6'],
            ['name' => 'Kesehatan',         'icon' => '💊', 'color' => '#ef4444'],
            ['name' => 'Tagihan & Utilitas','icon' => '💡', 'color' => '#eab308'],
            ['name' => 'Hiburan',           'icon' => '🎬', 'color' => '#8b5cf6'],
            ['name' => 'Pendidikan',        'icon' => '📚', 'color' => '#06b6d4'],
            ['name' => 'Lainnya',           'icon' => '📦', 'color' => '#6b7280'],
        ];

        foreach ($defaults as $category) {
            Category::firstOrCreate(
                ['name' => $category['name'], 'user_id' => null],
                array_merge($category, ['is_default' => true])
            );
        }
    }
}
