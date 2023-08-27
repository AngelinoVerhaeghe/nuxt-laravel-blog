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
			'slug' => $this->slug,
			'description' => strip_tags($this->description),
			'imageUrl' => $mediaUrl,
			'isPublished' => $this->is_published,
			'createAt' => $this->created_at->toFormattedDateString(),
			'author' => [
				'name' => $this->author->name,
			],
			'category' => [
				'name' => $this->category->name,
			],
		];
	}
}
