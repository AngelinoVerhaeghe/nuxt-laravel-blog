<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\Filament\Resources\ProductResource\Pages\ListProducts;
use App\Filament\Resources\ProductResource\Pages\CreateProduct;
use App\Filament\Resources\ProductResource\Pages\EditProduct;
use App\Enums\ProductTypeEnum;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;

class ProductResource extends Resource
{
	protected static ?string $model = Product::class;

	protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-globe-alt';

	protected static ?int $navigationSort = 0;

	protected static string | \UnitEnum | null $navigationGroup = 'Shop';

	// Setup Global Search trough whole application, filter on 'name' column property
	protected static ?string $recordTitleAttribute = 'name';

	// Set limit on the search results
	protected static int $globalSearchResultsLimit = 20;

	// Navigation Customization
	//protected static ?string $activeNavigationIcon = 'heroicon-o-check-badge';

	// Badges on navigation
	public static function getNavigationBadge(): ?string
	{
		return static::getModel()::count();
	}

	// Override to add more searchable columns
	public static function getGloballySearchableAttributes(): array
	{
		return [ 'name', 'slug', 'description' ];
	}

	// Add additional information to the global search, bv
	// U search a product, and it shows the 'Brand' to in the result
	public static function getGlobalSearchResultDetails( Model $record ): array
	{
		return [
			'Brand' => $record->brand->name,
		];
	}

	public static function getGlobalSearchEloquentQuery(): Builder
	{
		return parent::getGlobalSearchEloquentQuery()->with([ 'brand' ]);
	}

	public static function form( Schema $schema ): Schema
	{
		return $schema
			->components([
				Group::make()
					->schema([
						Section::make()
							->schema([
								TextInput::make('name')
									->required()
									->live(onBlur: true)
									->unique(ignoreRecord: true)
									->afterStateUpdated(function ( string $operation, $state, Set $set ) {
										if ( $operation !== 'create' ) {
											return;
										}

										$set('slug', Str::slug($state));
									}),
								TextInput::make('slug')
									->disabled()
									->dehydrated()
									->required()
									->unique(Product::class, 'slug', ignoreRecord: true),
								MarkdownEditor::make("description")->columnSpan('full')
							])->columns(2),
						Section::make('Pricing & Inventory')
							->schema([
								TextInput::make('sku')
									->label('SKU (Stock Keeping Unit)')
									->unique(ignoreRecord: true)
									->required(),
								TextInput::make('price')
									->numeric()
									->rules([ 'regex:/^\d{1,6}(\.\d{0,2})?$/' ])
									->required(),
								TextInput::make('quantity')
									->numeric()
									->minValue(0)
									->maxValue(9999),
								Select::make('type')
									->options([
										'Pop!' => ProductTypeEnum::POP->value,
										'Pins' => ProductTypeEnum::PINS->value,
										'Mini Figures' => ProductTypeEnum::MINI_FIGURES->value,
										'Games' => ProductTypeEnum::GAMES->value,
										'Accessories' => ProductTypeEnum::ACCESSORIES->value,
									])
							])->columns(2)
					]),
				Group::make()
					->schema([
						Section::make('Status')
							->schema([
								Toggle::make('is_visible')
									->label('Visibility')
									->helperText('Enable or disable product visibility')
									->default(true),
								Toggle::make('is_featured')
									->label('Featured')
									->helperText('Enable or disable products featured status'),
								DatePicker::make('published_at')
									->label('Availability')
									->default(now())
							]),
						Section::make('Image')
							->schema([
								FileUpload::make('image')
									->directory('pops')
									->preserveFilenames()
									->image()
									->imageEditor()
							]),
						Section::make('Associations')
							->schema([
								Select::make('brand_id')
									->relationship('brand', 'name')
									->required(),
								Select::make('categories')
									->relationship('categories', 'name')
									->multiple()
									->required()
							])
					]),
			]);
	}

	public static function table( Table $table ): Table
	{
		return $table
			->columns([
				ImageColumn::make('image'),
				TextColumn::make('name')
					->searchable()
					->sortable(),
				TextColumn::make('brand.name')
					->searchable()
					->sortable()
					->toggleable(),
				IconColumn::make('is_visible')
					->sortable()
					->toggleable()
					->label('Visibility')
					->boolean(),
				TextColumn::make('price')
					->sortable()
					->toggleable(),
				TextColumn::make('quantity')
					->sortable()
					->toggleable(),
				TextColumn::make('published_at')
					->sortable()
					->date(),
				TextColumn::make('type')
					->sortable()
					->toggleable()
			])
			->filters([
				TernaryFilter::make('is_visible')
					->label('Visibility')
					->boolean()
					->trueLabel('Only Visible Products')
					->falseLabel('Only Hidden Products')
					->native(false),
				SelectFilter::make('brand')
					->relationship('brand', 'name')
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
			'index' => ListProducts::route('/'),
			'create' => CreateProduct::route('/create'),
			'edit' => EditProduct::route('/{record}/edit'),
		];
	}
}
