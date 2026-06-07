<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Sembako',
            'Makanan Ringan',
            'Minuman',
            'Perawatan Rumah',
            'Perawatan Diri',
            'Buah dan Sayur',
            'Produk Beku',
            'Perlengkapan Bayi',
            'Bumbu Masak',
            'Kesehatan',
        ]);

        return [
            'name' => $name,
            'slug' => str($name)->slug(),
        ];
    }
}
