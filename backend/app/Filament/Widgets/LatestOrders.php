<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\OrderResource;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;

class LatestOrders extends BaseWidget
{
	public static function canView(): bool
	{
		// Shop data is for admins only
		return auth()->user()?->hasRole('admin') ?? false;
	}

	protected static ?int $sort = 5;

	protected int | string | array $columnSpan = 'full';

	public function table( Table $table ): Table
	{
		return $table
			->heading('Latest orders')
			->query(OrderResource::getEloquentQuery())
			->defaultPaginationPageOption(5)
			->defaultSort('created_at', 'desc')
			->columns([
				TextColumn::make('number')
					->searchable()
					->sortable(),
				TextColumn::make('customer.name')
					->searchable()
					->sortable()
					->toggleable(),
				TextColumn::make('status')
					->badge()
					->searchable()
					->sortable(),
				TextColumn::make('created_at')
					->label('Order Date')
					->date()
			]);
	}
}
