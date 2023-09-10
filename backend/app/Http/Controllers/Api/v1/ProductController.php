<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
		$products = Product::all();

		return ProductResource::collection($products);
	}

	public function show(string $slug): JsonResponse|ProductResource
	{
		$product = Product::where('slug', $slug)->first();

		if (!$product) {
			return response()->json(['message' => 'Product not found'], 404);
		}

		return new ProductResource($product);
	}
}
