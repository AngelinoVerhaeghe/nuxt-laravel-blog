<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Wizard;
use Filament\Schemas\Components\Wizard\Step;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Forms\Components\Placeholder;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\Filament\Resources\OrderResource\Pages\ListOrders;
use App\Filament\Resources\OrderResource\Pages\CreateOrder;
use App\Filament\Resources\OrderResource\Pages\EditOrder;
use App\Enums\OrderStatusEnum;
use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Models\Order;
use App\Models\Product;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class OrderResource extends Resource
{
	protected static ?string $model = Order::class;

	protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-shopping-bag';

	protected static ?int $navigationSort = 3;

	protected static string | \UnitEnum | null $navigationGroup = 'Shop';

	// Number of orders being processed
	public static function getNavigationBadge(): ?string
	{
		return static::getModel()::where('status', OrderStatusEnum::PROCESSING)->count();
	}

	// Turns orange once more than 10 orders are being processed
	public static function getNavigationBadgeColor(): string|array|null
	{
		return static::getNavigationBadge() > 10 ? 'warning' : 'primary';
	}

	public static function form( Schema $schema ): Schema
	{
		return $schema
			->components([
				Wizard::make([
					Step::make('Order Details')
						->schema([
							TextInput::make('number')
								->default('OR-' . random_int(100000, 9999999))
								->disabled()
								->dehydrated()
								->required(),
							Select::make('customer_id')
								->relationship('customer', 'name')
								->searchable()
								->required(),
							TextInput::make('shipping_price')
							->label('Shipping Costs')
							->dehydrated()
							->numeric()
							->required(),
							Select::make('status')
								->options(OrderStatusEnum::class)
								->default(OrderStatusEnum::PENDING)
								->required(),
							MarkdownEditor::make('notes')
								->columnSpanFull()
						])->columns(2),
					Step::make('Order Items')
						->schema([
							Repeater::make('items')
								->relationship()
								->schema([
									Select::make('product_id')
										->label('Product')
										->options(Product::query()->pluck('name', 'id'))
										->required()
										->live()
										->afterStateUpdated(fn( $state, Set $set ) => $set('unit_price', Product::find($state)?->price ?? 0)),
									TextInput::make('quantity')
										->numeric()
										->live()
										->dehydrated()
										->default(1)
										->required(),
									TextInput::make('unit_price')
										->label('Unit Price')
										->disabled()
										->dehydrated()
										->numeric()
										->required(),
									Placeholder::make('total_price')
										->label('Total Price')
										->content(function ( $get ) {
											return $get('quantity') * $get('unit_price');
										})
								])->columns(4)
						])
				])->columnSpanFull()
			]);
	}

	public static function table( Table $table ): Table
	{
		return $table
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
			])
			->filters([
				//
			])
			->recordActions([
				ActionGroup::make([
					ViewAction::make(),
					EditAction::make(),
					DeleteAction::make()
				])
			])
			->toolbarActions([
				BulkActionGroup::make([
					ExportBulkAction::make(),
					DeleteBulkAction::make(),
				]),
			])
			->emptyStateActions([
				CreateAction::make(),
			]);
	}

	public static function getRelations(): array
	{
		return [
			//
		];
	}

	public static function getPages(): array
	{
		return [
			'index' => ListOrders::route('/'),
			'create' => CreateOrder::route('/create'),
			'edit' => EditOrder::route('/{record}/edit'),
		];
	}
}
