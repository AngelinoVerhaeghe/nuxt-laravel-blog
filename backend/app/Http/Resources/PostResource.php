<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;
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
			// The full text only on a single post (GET /posts/{slug}), not in the list
			'content' => $this->when($request->route('post') !== null, fn () => $this->contentHtml()),
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

	/**
	 * The Markdown content as HTML. Older posts hold HTML from a rich text editor, so
	 * HTML is allowed through, then sanitized (no scripts, event handlers or javascript: links).
	 */
	private function contentHtml(): string
	{
		if (blank($this->content)) {
			return '';
		}

		return Str::sanitizeHtml(Str::markdown($this->content, ['html_input' => 'allow']));
	}
}
