<?php

namespace App\Filament\Resources\BrandResource\RelationManagers;

use App\Enums\ProductTypeEnum;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class ProductsRelationManager extends RelationManager
{
	protected static string $relationship = 'products';

	public function form( Form $form ): Form
	{
		return $form
			->schema([
				Forms\Components\Tabs::make('Products')
					->tabs([
						Forms\Components\Tabs\Tab::make('Information')
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
						Forms\Components\Tabs\Tab::make('Pricing & Inventory')
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
						Forms\Components\Tabs\Tab::make('Additional Information')
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
				Tables\Columns\ImageColumn::make('image'),
				Tables\Columns\TextColumn::make('name')
					->searchable()
					->sortable(),
				Tables\Columns\TextColumn::make('brand.name')
					->searchable()
					->sortable()
					->toggleable(),
				Tables\Columns\IconColumn::make('is_visible')
					->sortable()
					->toggleable()
					->label('Visibility')
					->boolean(),
				Tables\Columns\TextColumn::make('price')
					->sortable()
					->toggleable(),
				Tables\Columns\TextColumn::make('quantity')
					->sortable()
					->toggleable(),
				Tables\Columns\TextColumn::make('published_at')
					->sortable()
					->date(),
				Tables\Columns\TextColumn::make('type')
					->sortable()
					->toggleable()
			])
			->filters([
				//
			])
			->headerActions([
				Tables\Actions\CreateAction::make(),
			])
			->actions([
				Tables\Actions\ActionGroup::make([
					Tables\Actions\ViewAction::make(),
					Tables\Actions\EditAction::make(),
					Tables\Actions\DeleteAction::make()
				])
			])
			->bulkActions([
				Tables\Actions\BulkActionGroup::make([
					Tables\Actions\DeleteBulkAction::make(),
				]),
			])
			->emptyStateActions([
				Tables\Actions\CreateAction::make(),
			]);
	}
}
