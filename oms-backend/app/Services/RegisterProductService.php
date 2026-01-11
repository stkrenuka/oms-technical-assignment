<?php
namespace App\Services;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
class RegisterProductService
{
    // Admin: all products
    public function getAdminProducts(Request $request, int $perPage = 10)
    {
        $query = Product::query();
        if ($request->filled('search')) {
            $words = preg_split('/\s+/', trim($request->search));
            foreach ($words as $word) {
                $query->where(function ($q) use ($word) {
                    $q->where('name', 'LIKE', "%{$word}%")
                        ->orWhere('description', 'LIKE', "%{$word}%");
                });
            }
        }
        return $query->latest()->paginate($perPage);
    }
    // Customer: only active products
    public function getActiveProducts(Request $request, int $perPage = 10)
    {
        $query = Product::where('status', 'active');
        if ($request->filled('search')) {
            $words = preg_split('/\s+/', trim($request->search));
            foreach ($words as $word) {
                $query->where(function ($q) use ($word) {
                    $q->where('name', 'LIKE', "%{$word}%")
                        ->orWhere('description', 'LIKE', "%{$word}%");
                });
            }
        }
        return $query->latest()->paginate($perPage);
    }
    public function create(Request $request): Product
    {
        $data = $request->only([
            'name',
            'description',
            'price',
            'stock',
            'status',
            'sku',
        ]);
        // Image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request
                ->file('image')
                ->store('products', 'public');
        }
        return Product::create($data);
    }
    public function update(Product $product, array $data): Product
    {
        if (isset($data['image'])) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $data['image']
                ->store('products/images', 'public');
        }
        $product->update($data);
        return $product;
    }
}
