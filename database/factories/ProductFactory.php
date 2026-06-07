<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = fake()->randomElement([
            ['Beras Ramos 5 kg', 'Beras pulen untuk kebutuhan makan keluarga.', 68000],
            ['Gula Pasir 1 kg', 'Gula pasir putih untuk minuman dan masakan.', 16500],
            ['Minyak Goreng 2 L', 'Minyak goreng kemasan untuk memasak harian.', 36000],
            ['Tepung Terigu 1 kg', 'Tepung serbaguna untuk kue dan gorengan.', 13500],
            ['Keripik Kentang Original 120 g', 'Keripik kentang renyah rasa original.', 18000],
            ['Biskuit Cokelat 300 g', 'Biskuit isi krim cokelat untuk camilan.', 14500],
            ['Air Mineral 1.5 L', 'Air mineral botol ukuran keluarga.', 6500],
            ['Susu UHT Cokelat 1 L', 'Susu UHT rasa cokelat siap minum.', 21000],
            ['Deterjen Bubuk 1 kg', 'Deterjen bubuk untuk mencuci pakaian.', 23000],
            ['Sabun Cuci Piring 750 ml', 'Sabun cair pembersih lemak pada piring.', 15500],
            ['Sabun Mandi Cair 450 ml', 'Sabun mandi cair dengan aroma lembut.', 28000],
            ['Pasta Gigi 190 g', 'Pasta gigi untuk perlindungan gigi harian.', 17500],
        ]);

        return [
            'category_id' => Category::factory(),
            'name' => $product[0],
            'description' => $product[1],
            'price' => $product[2],
            'stock' => fake()->numberBetween(0, 100),
            'image' => null,
        ];
    }
}
