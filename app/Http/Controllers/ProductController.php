<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::latest()->get();

        return view('admin.products.index', compact('products'));
    }

  
    public function create()
    {
        return view('admin.products.create');
    }

   
    public function store(Request $request)
    {
         $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
        ]);

        Product::create($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }
    

  
    public function show(Product $product)
    {
        //
    }

   
    public function edit(Product $product)
    {
        return view('admin.products.edit', compact('product'));
    }

    
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'product_name' => ['required', 'string', 'max:255'],
        ]);

        $product->update($validated);

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    
    public function destroy(Product $product)
    {
         $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('success', 'Product deleted successfully.');
    }
    
}
