<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\SeoSchemaService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private readonly SeoSchemaService $seo) {}

    public function show(Request $request, string $slug)
    {
        if (in_array($slug, config('jamsora.reserved_slugs', []), true)) {
            abort(404);
        }

        $category = Category::query()
            ->where('slug', $slug)
            ->where('status', 'active')
            ->with(['faqs', 'children'])
            ->firstOrFail();

        $categoryIds = collect([$category->id])
            ->merge($category->children->pluck('id'))
            ->all();

        $query = Product::query()
            ->where('status', 'published')
            ->whereIn('category_id', $categoryIds)
            ->with(['images', 'category']);

        if ($shape = $request->string('shape')->toString()) {
            $query->where(function ($q) use ($shape) {
                $q->where('shape', $shape)->orWhereJsonContains('gem_attributes->shape', $shape);
            });
        }

        if ($metal = $request->string('metal')->toString()) {
            $query->where('metal_type', $metal);
        }

        $sort = $request->string('sort', 'newest')->toString();
        match ($sort) {
            'price_asc' => $query->orderByRaw('COALESCE(sale_price, price) asc'),
            'price_desc' => $query->orderByRaw('COALESCE(sale_price, price) desc'),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        $shapes = Product::query()
            ->whereIn('category_id', $categoryIds)
            ->whereNotNull('shape')
            ->distinct()
            ->pluck('shape');

        $meta = $this->seo->metaForCategory($category);
        $schemas = [
            $this->seo->organization(),
            $this->seo->breadcrumbs([
                ['name' => 'Home', 'url' => route('home')],
                ['name' => $category->name, 'url' => url('/'.$category->slug)],
            ]),
        ];

        if ($category->faqs->isNotEmpty()) {
            $schemas[] = $this->seo->faqPage($category->faqs);
        }

        return view('storefront.categories.show', compact(
            'category', 'products', 'shapes', 'meta', 'schemas'
        ));
    }
}
