<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Post extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;

	protected $fillable = ['user_id', 'category_id', 'title', 'slug', 'description', 'is_published'];

	protected $casts = [
		'is_published' => 'boolean',
	];

	public function author(): BelongsTo
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function category(): BelongsTo
	{
		return $this->belongsTo(Category::class);
	}

	public function tags(): BelongsToMany
	{
		return $this->belongsToMany(Tag::class);
	}
}
