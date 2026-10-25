<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use App\Enums\ProductTypeEnum;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductsRelationManager extends RelationManager
{
	protected static string $relationship = 'products';

	public function form( Schema $schema ): Schema
	{
		return $schema
			->components([
				Tabs::make('Products')
					->tabs([
						Tab::make('Information')
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
						Tab::make('Pricing & Inventory')
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
							])->columns(2),
						Tab::make('Additional Information')
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
									->default(now()),
								Select::make('categories')
									->relationship('categories', 'name')
									->multiple()
									->required(),
								FileUpload::make('image')
									->directory('pops')
									->preserveFilenames()
									->image()
									->imageEditor()
									->columnSpanFull()
							])->columns(2)
					])->columnSpanFull()
			]);
	}

	public function table( Table $table ): Table
	{
		return $table
			->recordTitleAttribute('name')
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
				//
			])
			->headerActions([
				CreateAction::make(),
			])
			->recordActions([
				ActionGroup::make([
					EditAction::make(),
					DeleteAction::make(),
				])
			])
			->toolbarActions([
				BulkActionGroup::make([
					DeleteBulkAction::make(),
				]),
			])
			->emptyStateActions([
				CreateAction::make(),
			]);
	}
}
