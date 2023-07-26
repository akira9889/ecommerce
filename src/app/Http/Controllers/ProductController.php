<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()
        ->where('published', true)
        ->orderBy('updated_at', 'desc')
        ->paginate(20);

        return view('product.index', compact('products'));
    }

    public function view(Product $product)
    {
        return view('product.view', ['product' => $product]);
    }
}
