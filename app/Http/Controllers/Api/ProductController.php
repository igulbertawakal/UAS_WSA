<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Daftar produk
     *
     * Produk ditampilkan dengan kategori, pagination, dan pencarian.
     *
     * @group Products
     *
     * @authenticated
     *
     * @queryParam search string Pencarian nama atau deskripsi produk. Example: laptop
     * @queryParam category_id integer Filter berdasarkan ID kategori. Example: 1
     * @queryParam per_page integer Jumlah item per halaman, maksimal 50. Example: 10
     * @queryParam page integer Halaman pagination. Example: 1
     *
     * @response 200 {"current_page":1,"data":[{"id":1,"category_id":1,"name":"Laptop ASUS","description":"Laptop kerja ringan.","price":"7500000.00","stock":10,"image":null,"image_url":null,"category":{"id":1,"name":"Elektronik","slug":"elektronik"}}],"total":30,"per_page":10}
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->integer('per_page', 10), 1), 50);

        $products = Product::query()
            ->with('category')
            ->when($request->filled('search'), function ($query) use ($request): void {
                $search = $request->string('search')->toString();
                $query->where(function ($query) use ($search): void {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('category_id'), function ($query) use ($request): void {
                $query->where('category_id', $request->integer('category_id'));
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return response()->json($products);
    }

    /**
     * Tambah produk
     *
     * Menerima gambar produk dan menyimpannya di local public storage.
     *
     * @group Products
     *
     * @authenticated
     *
     * @bodyParam category_id integer required ID kategori. Example: 1
     * @bodyParam name string required Nama produk. Example: Laptop ASUS
     * @bodyParam description string Deskripsi produk. Example: Laptop kerja ringan.
     * @bodyParam price numeric required Harga produk. Example: 7500000
     * @bodyParam stock integer required Jumlah stok. Example: 10
     * @bodyParam image file Gambar JPG atau PNG, maksimal 2 MB.
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate($this->validationRules());

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($data)->load('category');

        return response()->json([
            'message' => 'Produk berhasil ditambahkan.',
            'data' => $product,
        ], 201);
    }

    /**
     * Detail produk
     *
     * Menampilkan produk dengan kategori dalam nested JSON.
     *
     * @group Products
     *
     * @authenticated
     *
     * @response 200 {"id":1,"category_id":1,"name":"Laptop ASUS","description":"Laptop kerja ringan.","price":"7500000.00","stock":10,"image":"products/laptop.jpg","image_url":"http://localhost/UAS_WSA/public/storage/products/laptop.jpg","category":{"id":1,"name":"Elektronik","slug":"elektronik"}}
     */
    public function show(Product $product): JsonResponse
    {
        return response()->json($product->load('category'));
    }

    /**
     * Ubah produk
     *
     * Gunakan POST dengan field `_method=PUT` di Postman untuk upload file multipart.
     *
     * @group Products
     *
     * @authenticated
     *
     * @bodyParam category_id integer required ID kategori. Example: 1
     * @bodyParam name string required Nama produk. Example: Laptop ASUS Update
     * @bodyParam description string Deskripsi produk.
     * @bodyParam price numeric required Harga produk. Example: 7200000
     * @bodyParam stock integer required Jumlah stok. Example: 8
     * @bodyParam image file Gambar baru JPG atau PNG, maksimal 2 MB.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $data = $request->validate($this->validationRules());

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }

            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return response()->json([
            'message' => 'Produk berhasil diperbarui.',
            'data' => $product->fresh()->load('category'),
        ]);
    }

    /**
     * Hapus produk
     *
     * Data produk dan file gambarnya akan dihapus.
     *
     * @group Products
     *
     * @authenticated
     */
    public function destroy(Product $product): JsonResponse
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus.',
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validationRules(): array
    {
        return [
            'category_id' => ['required', 'integer', Rule::exists('categories', 'id')],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ];
    }
}
