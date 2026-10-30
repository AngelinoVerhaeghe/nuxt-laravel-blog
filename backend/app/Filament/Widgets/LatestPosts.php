<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestPosts extends BaseWidget
{
	protected static ?int $sort = 2;

	protected int | string | array $columnSpan = 'full';

	public function table( Table $table ): Table
	{
		return $table
			->heading('Latest posts')
			->query(PostResource::getEloquentQuery()->with([ 'author', 'category' ]))
			->defaultPaginationPageOption(5)
			->defaultSort('created_at', 'desc')
			->recordUrl(fn (Post $record): string => PostResource::getUrl('edit', [ 'record' => $record ]))
			->columns([
				TextColumn::make('title')
					->searchable()
					->weight('medium'),
				TextColumn::make('author.name')
					->toggleable(),
				TextColumn::make('category.name')
					->toggleable(),
				TextColumn::make('is_published')
					->label('Status')
					->badge()
					->formatStateUsing(fn (bool $state): string => $state ? 'Published' : 'Draft')
					->color(fn (bool $state): string => $state ? 'success' : 'gray'),
				TextColumn::make('created_at')
					->label('Created')
					->since()
					->sortable(),
			]);
	}
}
