<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->latest()->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    public function create()
    {
        return view('admin.products.form', [
            'product' => new Product,
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']);
        $product = Product::create($data);
        $this->syncTags($product, $request);
        $this->syncImage($product, $request);

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $product->load(['tags', 'images']);

        return view('admin.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $data = $this->validated($request);
        $data['slug'] = Str::slug($data['name']);
        $product->update($data);
        $this->syncTags($product, $request);
        $this->syncImage($product, $request);

        return redirect()->route('admin.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'stock_qty' => ['required', 'integer', 'min:0'],
            'manage_stock' => ['boolean'],
            'status' => ['required', 'in:draft,published'],
            'is_featured' => ['boolean'],
            'image_url' => ['nullable', 'url'],
            'gem_shape' => ['nullable', 'string'],
            'gem_carat' => ['nullable', 'string'],
            'gem_certificate' => ['nullable', 'string'],
        ]) + [
            'manage_stock' => $request->boolean('manage_stock'),
            'is_featured' => $request->boolean('is_featured'),
            'gem_attributes' => array_filter([
                'shape' => $request->gem_shape,
                'carat' => $request->gem_carat,
                'certificate' => $request->gem_certificate,
            ]),
        ];
    }

    private function syncTags(Product $product, Request $request): void
    {
        $product->tags()->sync($request->input('tags', []));
    }

    private function syncImage(Product $product, Request $request): void
    {
        if (! $request->filled('image_url')) {
            return;
        }

        $product->images()->updateOrCreate(
            ['is_primary' => true],
            ['path' => $request->image_url, 'alt_text' => $product->name, 'sort_order' => 0, 'is_primary' => true]
        );
    }
}
