<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@uas-wsa.test'],
            [
                'name' => 'Administrator',
                'password' => 'password',
            ]
        );

        Product::query()->delete();
        Category::query()->delete();

        $supermarket = [
            'Sembako' => [
                ['Beras Ramos 5 kg', 'Beras pulen untuk kebutuhan makan keluarga.', 68000, 45],
                ['Gula Pasir 1 kg', 'Gula pasir putih untuk minuman dan masakan.', 16500, 80],
                ['Minyak Goreng 2 L', 'Minyak goreng kemasan untuk memasak harian.', 36000, 60],
                ['Tepung Terigu 1 kg', 'Tepung serbaguna untuk kue dan gorengan.', 13500, 70],
                ['Telur Ayam 1 kg', 'Telur ayam segar isi sekitar 15 butir.', 29000, 35],
                ['Garam Dapur 500 g', 'Garam halus beryodium untuk bumbu masakan.', 6000, 90],
            ],
            'Makanan Ringan' => [
                ['Keripik Kentang Original 120 g', 'Keripik kentang renyah rasa original.', 18000, 55],
                ['Biskuit Cokelat 300 g', 'Biskuit isi krim cokelat untuk camilan.', 14500, 65],
                ['Wafer Keju 145 g', 'Wafer renyah dengan krim rasa keju.', 12500, 75],
                ['Kacang Panggang 250 g', 'Kacang panggang gurih siap santap.', 22000, 40],
                ['Roti Tawar Kupas', 'Roti tawar lembut untuk sarapan.', 17000, 30],
                ['Mie Instan Goreng', 'Mie instan rasa goreng untuk stok rumah.', 3500, 120],
            ],
            'Minuman' => [
                ['Air Mineral 1.5 L', 'Air mineral botol ukuran keluarga.', 6500, 100],
                ['Susu UHT Cokelat 1 L', 'Susu UHT rasa cokelat siap minum.', 21000, 50],
                ['Teh Celup Melati 25 pcs', 'Teh celup aroma melati untuk minuman hangat.', 9500, 70],
                ['Kopi Bubuk 200 g', 'Kopi bubuk pilihan untuk seduhan harian.', 24000, 45],
                ['Sirup Cocopandan 600 ml', 'Sirup rasa cocopandan untuk minuman segar.', 23000, 35],
                ['Minuman Isotonik 500 ml', 'Minuman isotonik untuk membantu hidrasi.', 8000, 60],
            ],
            'Perawatan Rumah' => [
                ['Deterjen Bubuk 1 kg', 'Deterjen bubuk untuk mencuci pakaian.', 23000, 55],
                ['Sabun Cuci Piring 750 ml', 'Sabun cair pembersih lemak pada piring.', 15500, 65],
                ['Pembersih Lantai 800 ml', 'Cairan pembersih lantai aroma segar.', 18000, 50],
                ['Tisu Dapur 2 Roll', 'Tisu serbaguna untuk membersihkan dapur.', 19500, 40],
                ['Pewangi Pakaian 900 ml', 'Pewangi pakaian konsentrat tahan lama.', 21000, 45],
                ['Kantong Sampah 15 pcs', 'Kantong sampah ukuran sedang untuk rumah.', 12000, 80],
            ],
            'Perawatan Diri' => [
                ['Sabun Mandi Cair 450 ml', 'Sabun mandi cair dengan aroma lembut.', 28000, 45],
                ['Sampo Anti Ketombe 340 ml', 'Sampo untuk membantu mengurangi ketombe.', 36000, 35],
                ['Pasta Gigi 190 g', 'Pasta gigi untuk perlindungan gigi harian.', 17500, 60],
                ['Sikat Gigi Medium', 'Sikat gigi bulu medium untuk dewasa.', 12000, 70],
                ['Deodoran Roll On 50 ml', 'Deodoran roll on untuk perlindungan harian.', 24500, 40],
                ['Tisu Basah 50 sheets', 'Tisu basah praktis untuk kebersihan diri.', 16000, 55],
            ],
        ];

        foreach ($supermarket as $categoryName => $products) {
            $category = Category::create([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName),
            ]);

            foreach ($products as [$name, $description, $price, $stock]) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $name,
                    'description' => $description,
                    'price' => $price,
                    'stock' => $stock,
                    'image' => null,
                ]);
            }
        }
    }
}
