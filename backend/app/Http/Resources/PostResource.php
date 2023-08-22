<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class PostResource extends JsonResource implements HasMedia
{
	use  InteractsWithMedia;
	/**
	 * Transform the resource collection into an array.
	 *
	 * @return array<int|string, mixed>
	 */
	public function toArray( Request $request ): array
	{
		$mediaObject = $this->getFirstMedia('posts');
		$mediaUrl = $mediaObject?->getFullUrl();

		return [
			'id' => $this->id,
			'title' => $this->title,
			'description' => $this->description,
			'imageUrl' => $mediaUrl,
			'isPublished' => $this->is_published,
			'createAt' => $this->created_at,
			'author' => [
				'name' => $this->author->name,
			],
			'category' => [
				'name' => $this->category->name,
			],
		];
	}
}

//"media": [
//{
//	"id": 1,
//"model_type": "App\\Models\\Post",
//"model_id": 1,
//"uuid": "abb0c2ea-0b48-472c-b898-08564a971714",
//"collection_name": "posts",
//"name": "image_png (2) (1)",
//"file_name": "FqczagN7UCGGjLk3BCH1GLtep5DK96-metaaW1hZ2VfcG5nICgyKSAoMSkucG5n-.png",
//"mime_type": "image/png",
//"disk": "public",
//"conversions_disk": "public",
//"size": 498802,
//"manipulations": [],
//"custom_properties": [],
//"generated_conversions": [],
//"responsive_images": [],
//"order_column": 1,
//"created_at": "2023-08-19T18:14:33.000000Z",
//"updated_at": "2023-08-19T18:14:33.000000Z",
//"original_url": "http://localhost:8000/storage/1/FqczagN7UCGGjLk3BCH1GLtep5DK96-metaaW1hZ2VfcG5nICgyKSAoMSkucG5n-.png",
//"preview_url": ""
//}
//]
