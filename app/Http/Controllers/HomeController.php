<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::latest()->take(4)->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->nama_product,
                'weight' => $product->weight . ' ' . $product->satuan,
                'price' => 'Rp ' . number_format($product->harga, 0, ',', '.'),
                'rating' => '4.' . rand(5, 9), // Placeholder rating
                'foto_product' => $product->foto_product,
                'icon' => 'local_cafe', // Fallback icon
            ];
        });

        return Inertia::render('Home', [
            'products' => $products,
        ]);
    }
}
