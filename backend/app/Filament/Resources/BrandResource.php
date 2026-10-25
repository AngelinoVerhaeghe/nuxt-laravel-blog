<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\Filament\Resources\BrandResource\RelationManagers\ProductsRelationManager;
use App\Filament\Resources\BrandResource\Pages\ListBrands;
use App\Filament\Resources\BrandResource\Pages\CreateBrand;
use App\Filament\Resources\BrandResource\Pages\EditBrand;
use App\Filament\Resources\BrandResource\Pages;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Models\Brand;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class BrandResource extends Resource
{
	protected static ?string $model = Brand::class;

	protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';

	protected static ?int $navigationSort = 1;

	protected static string | \UnitEnum | null $navigationGroup = 'Shop';

	public static function form( Schema $schema ): Schema
	{
		return $schema
			->components([
				Group::make()
					->schema([
						Section::make([
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
								->unique(ignoreRecord: true),
							TextInput::make('url')
								->label('Website URL')
								->required()
								->unique(ignoreRecord: true)
								->columnSpan('full'),
							MarkdownEditor::make('description')
								->columnSpan('full'),
						])->columns(2)
					]),
				Group::make()
					->schema([
						Section::make('Status')
							->schema([
								Toggle::make('is_visible')
									->label('Visibility')
									->helperText('Enable or disable brand visibility')
									->default(true),
							]),
						Group::make()
							->schema([
								Section::make('Color')
									->schema([
										ColorPicker::make('primary_hex')
											->label('Primary Color')
									])
							]),
					])
			]);
	}

	public static function table( Table $table ): Table
	{
		return $table
			->columns([
				TextColumn::make('name')
					->searchable()
					->sortable(),
				TextColumn::make('url')
					->label('Website URL')
					->prefix('https://www.')
					->sortable()
					->searchable(),
				ColorColumn::make('primary_hex')
					->label('Primary Color'),
				IconColumn::make('is_visible')
					->boolean()
					->sortable()
					->label('Visibility'),
				TextColumn::make('updated_at')
					->date()
					->sortable(),
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
			'index' => ListBrands::route('/'),
			'create' => CreateBrand::route('/create'),
			'edit' => EditBrand::route('/{record}/edit'),
		];
	}
}
