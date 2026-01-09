<?php

namespace App\Http\Controllers;
use App\Services\RegisterProductService ;
use Illuminate\Http\Request;

class ProductController extends Controller
{
     protected RegisterProductService $productService;
    //
     public function __construct(RegisterProductService $productService)
    {
        $this->productService = $productService;
    }
    public function index()
{
    return response()->json(
        $this->productService->getActiveProducts()
    );
}
}
