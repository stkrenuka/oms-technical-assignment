<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Services\RegisterProductService;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreProductRequest;
class ProductController extends Controller
{
    protected $productService;
    public function __construct(RegisterProductService $productService)
    {
        $this->productService = $productService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);
        $products = $this->productService->getAdminProducts($request);
        return response()->json($products);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, array &$data)
    {
        //
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('products', 'public');
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreProductRequest $request,
        RegisterProductService $productService
    ) {
        $product = $productService->create($request);
        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product,
        ], 201);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, string $id)
    {
        $product = Product::findOrFail($id);
        $product = $this->productService
            ->update($product, $request->validated());
        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = Auth::user();
        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'message' => 'Unauthorized. Only admin can delete products.'
            ], 403);
        }
        $product = Product::findOrFail($id);
        $product->delete(); // soft delete
        return response()->json([
            'message' => 'Product deleted successfully'
        ], 200);
    }
    public function changeStatus(Request $request, Product $product)
    {
        $request->validate([
            'status' => 'required|string|in:active,inactive',
        ]);
        $product->update([
            'status' => $request->status,
        ]);
        return response()->json([
            'message' => 'Product status updated successfully',
            'status' => $product->status,
        ]);
    }
}
