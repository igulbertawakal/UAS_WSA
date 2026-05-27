<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    /**
     * Daftar kategori
     *
     * @group Categories
     *
     * @authenticated
     *
     * @queryParam page integer Halaman pagination. Example: 1
     *
     * @response 200 {"current_page":1,"data":[{"id":1,"name":"Elektronik","slug":"elektronik","products_count":6}],"total":5}
     */
    public function index(): JsonResponse
    {
        return response()->json(Category::withCount('products')->paginate(10));
    }

    /**
     * Tambah kategori
     *
     * @group Categories
     *
     * @authenticated
     *
     * @bodyParam name string required Nama kategori yang unik. Example: Elektronik
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:categories,name'],
        ]);

        $category = Category::create([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return response()->json([
            'message' => 'Kategori berhasil ditambahkan.',
            'data' => $category,
        ], 201);
    }

    /**
     * Detail kategori
     *
     * Menampilkan kategori beserta daftar produknya.
     *
     * @group Categories
     *
     * @authenticated
     *
     * @response 200 {"id":1,"name":"Elektronik","slug":"elektronik","products":[{"id":1,"category_id":1,"name":"Laptop ASUS","price":"7500000.00","stock":10}]}
     */
    public function show(Category $category): JsonResponse
    {
        return response()->json($category->load('products'));
    }

    /**
     * Ubah kategori
     *
     * @group Categories
     *
     * @authenticated
     *
     * @bodyParam name string required Nama kategori. Example: Elektronik Rumah
     */
    public function update(Request $request, Category $category): JsonResponse
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->ignore($category->id),
            ],
        ]);

        $category->update([
            'name' => $data['name'],
            'slug' => Str::slug($data['name']),
        ]);

        return response()->json([
            'message' => 'Kategori berhasil diperbarui.',
            'data' => $category,
        ]);
    }

    /**
     * Hapus kategori
     *
     * @group Categories
     *
     * @authenticated
     */
    public function destroy(Category $category): JsonResponse
    {
        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'Kategori yang masih memiliki produk tidak dapat dihapus.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Kategori berhasil dihapus.',
        ]);
    }
}
