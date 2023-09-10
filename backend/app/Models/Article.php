<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

	protected $fillable = ['author_id', 'title', 'body', 'published_at'];

	public function author(): BelongsTo
	{
		return $this->belongsTo(Author::class);
	}

	public function categories(): HasMany
	{
		return $this->hasMany(Category::class);
	}
}
