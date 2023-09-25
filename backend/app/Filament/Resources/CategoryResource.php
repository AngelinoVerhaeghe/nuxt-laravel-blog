<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
	protected static ?string $model = Category::class;

	protected static ?string $navigationIcon = 'heroicon-o-swatch';

	// Create a dropdown group in the sidebar
	protected static ?string $navigationGroup = 'Extra';

	// Place this dropdown item in an order u want
	protected static ?int $navigationSort = 1;

	// Don't show an item on the sidebar
	//protected static bool $shouldRegisterNavigation = false;

	public static function form( Form $form ): Form
	{
		return $form
			->schema([
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
								MarkdownEditor::make('description')
									->columnSpanFull()
							])->columns(2)
					]),

				Group::make()
					->schema([
						Section::make('Status')
							->schema([
								Toggle::make('is_visible')
									->label('Visibility')
									->helperText('Enable or disable category visibility')
									->default(true),
								Select::make('parent_id')
									->relationship('parent', 'name')
							])
					])
			]);
	}

	public static function table( Table $table ): Table
	{
		return $table
			->columns([
				TextColumn::make('name')
					->sortable()
					->searchable(),
				TextColumn::make('parent.name')
					->label('Parent')
					->searchable()
					->sortable(),
				Tables\Columns\IconColumn::make('is_visible')
					->label('Visibility')
					->boolean()
					->sortable(),
				TextColumn::make('updated_at')
					->label('Updated Date')
					->date()
					->sortable(),
				//TextColumn::make('articles_count')->counts('articles')
			])
			->filters([
				//
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

	public static function getRelations(): array
	{
		return [
			RelationManagers\ProductsRelationManager::class
		];
	}

	public static function getPages(): array
	{
		return [
			'index' => Pages\ListCategories::route('/'),
			'create' => Pages\CreateCategory::route('/create'),
			'edit' => Pages\EditCategory::route('/{record}/edit'),
		];
	}
}
