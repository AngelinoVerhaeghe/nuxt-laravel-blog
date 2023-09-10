<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
			'id' => $this->id,
	        'brandId' => $this->brand_id,
	        'name' => $this->name,
	        'slug' => $this->slug,
	        'sku' => $this->sku,
	        'image' => $this->image,
	        'description' => $this->description,
	        'quantity' => $this->quantity,
	        'price' => $this->price,
	        'isVisible' => $this->is_visible,
	        'isFeatured' => $this->is_featured,
	        'publishedAt' => $this->published_at,
	        'brand' => [
				'name' => $this->brand->name
	        ]
        ];
    }
}
