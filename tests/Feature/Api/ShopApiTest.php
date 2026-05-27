<?php

namespace Tests\Feature\Api;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShopApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_login_and_protected_route_security(): void
    {
        $this->postJson('/api/register', [
            'name' => 'Mahasiswa',
            'email' => 'mahasiswa@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertCreated()->assertJsonStructure(['user', 'token']);

        $this->postJson('/api/login', [
            'email' => 'mahasiswa@example.com',
            'password' => 'password123',
        ])->assertOk()->assertJsonStructure(['user', 'token']);

        $this->getJson('/api/products')->assertUnauthorized();
    }

    public function test_authenticated_user_can_manage_products_with_image_and_search(): void
    {
        Storage::fake('public');
        Sanctum::actingAs(User::factory()->create());
        $category = Category::factory()->create(['name' => 'Elektronik', 'slug' => 'elektronik']);

        $response = $this->post('/api/products', [
            'category_id' => $category->id,
            'name' => 'Laptop Praktikum',
            'description' => 'Laptop untuk mengerjakan API.',
            'price' => 7500000,
            'stock' => 10,
            'image' => UploadedFile::fake()->create('laptop.jpg', 100, 'image/jpeg'),
        ])->assertCreated()->assertJsonPath('data.category.name', 'Elektronik');

        $product = Product::firstOrFail();
        $firstImage = $product->image;
        Storage::disk('public')->assertExists($firstImage);

        $this->getJson('/api/products?search=Laptop')
            ->assertOk()
            ->assertJsonPath('data.0.category.name', 'Elektronik')
            ->assertJsonPath('total', 1);

        $this->post("/api/products/{$product->id}", [
            '_method' => 'PUT',
            'category_id' => $category->id,
            'name' => 'Laptop Praktikum Update',
            'description' => 'Gambar sudah diperbarui.',
            'price' => 7200000,
            'stock' => 8,
            'image' => UploadedFile::fake()->create('laptop-baru.png', 100, 'image/png'),
        ])->assertOk()->assertJsonPath('data.name', 'Laptop Praktikum Update');

        $product->refresh();
        Storage::disk('public')->assertMissing($firstImage);
        Storage::disk('public')->assertExists($product->image);

        $this->deleteJson("/api/products/{$response->json('data.id')}")
            ->assertOk();

        Storage::disk('public')->assertMissing($product->image);
    }

    public function test_seeder_creates_required_dummy_records(): void
    {
        $this->seed();

        $this->assertDatabaseHas('users', ['email' => 'admin@uas-wsa.test']);
        $this->assertDatabaseCount('categories', 5);
        $this->assertDatabaseCount('products', 30);
    }
}
