<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\Filament\Resources\CategoryResource\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\CategoryResource\Pages\ListCategories;
use App\Filament\Resources\CategoryResource\Pages\CreateCategory;
use App\Filament\Resources\CategoryResource\Pages\EditCategory;
use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use App\Models\Product;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class CategoryResource extends Resource
{
	protected static ?string $model = Category::class;

	protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-swatch';

	// Create a dropdown group in the sidebar
	protected static string | \UnitEnum | null $navigationGroup = 'Extra';

	// Place this dropdown item in an order u want
	protected static ?int $navigationSort = 1;

	// Don't show an item on the sidebar
	//protected static bool $shouldRegisterNavigation = false;

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
				IconColumn::make('is_visible')
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
			->recordActions([
				ActionGroup::make([
					ViewAction::make(),
					EditAction::make(),
					DeleteAction::make()
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

	public static function getRelations(): array
	{
		return [
			ProductsRelationManager::class
		];
	}

	public static function getPages(): array
	{
		return [
			'index' => ListCategories::route('/'),
			'create' => CreateCategory::route('/create'),
			'edit' => EditCategory::route('/{record}/edit'),
		];
	}
}
