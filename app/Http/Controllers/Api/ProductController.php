<?php

namespace App\Http\Controllers\Api;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;

class ProductController extends Controller
{
    // Return all products with ProductResource
    public function index()
    {
        // Return a collection of products as an array of ProductResources
        return ProductResource::collection(Product::all());
    }

    // Store a new product and return a ProductResource
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $imagePath = $request->file('image')->store('products', 'public');
        // Create a new product without mass-assignment
        $product = new Product();
        $product->title = $request->title;
        $product->description = $request->description;
        $product->price = $request->price;
        // If you're handling an image upload, include this:
        $product->image = $imagePath;
        $product->save();

        // Return the created product as a ProductResource
        return new ProductResource($product);
    }

    // Show a single product with ProductResource
    public function show(Product $product)
    {
        // Return the single product as a ProductResource
        return new ProductResource($product);
    }

    // Update an existing product and return a ProductResource
    public function update(Request $request, Product $product)
    {
        // Validate input (only fields that are being updated)
        $request->validate([
            'title' => 'sometimes|required',
            'description' => 'sometimes|required',
            'price' => 'required|numeric',
            'image' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Manually update the product's fields
        if ($request->has('title')) {
            $product->title = $request->title;
        }
        if ($request->has('description')) {
            $product->description = $request->description;
        }
        if ($request->has('price')) {
            $product->price = $request->price;
        }

        // Check if an image file is being uploaded
        if ($request->hasFile('image')) {
            // Store the image and get the path
            $imagePath = $request->file('image')->store('products', 'public');
            // Update the product's image
            $product->image = $imagePath;
        }

        // Save the updated product
        $product->save();

        // Return the updated product as a ProductResource
        return new ProductResource($product);
    }

    // Delete a product
    public function destroy(Product $product)
    {
        // Delete the product
        $product->delete();

        // Return a successful response with no content
        return response()->json(null, 204);
    }
}
