<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category');

        if ($request->has('category') && $request->category !== '') {
            $categoryIds = explode(',', $request->category);
            $query->whereIn('category_id', $categoryIds);
        }

        if ($request->has('price') && $request->price !== '' && $request->price !== 'all') {
            $priceRange = $request->price;
            if ($priceRange === 'under_150') {
                $query->where('harga', '<', 150000);
            } elseif ($priceRange === '150_200') {
                $query->whereBetween('harga', [150000, 200000]);
            } elseif ($priceRange === '200_300') {
                $query->whereBetween('harga', [200000, 300000]);
            } elseif ($priceRange === 'above_300') {
                $query->where('harga', '>', 300000);
            }
        }

        $products = $query->orderBy('created_at', 'desc')->get();
        $categories = Category::all();

        return inertia('Shop/Index', [
            'products' => $products,
            'categories' => $categories,
            'selectedCategory' => $request->category ?? '',
            'selectedPrice' => $request->price ?? 'all',
        ]);     
    }

    public function show($id) {

        $product = Product::with('category')->where('id', $id)->first();

        if (!$product) {
            return abort(404);
        }

        return inertia('Shop/Show', [
            'product' => $product,
        ]);
    }
}
