<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Resources\CategoryResource\RelationManagers;
use App\Models\Category;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
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

	protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

	public static function form( Form $form ): Form
	{
		return $form
			->schema([
				Card::make()->schema([
					TextInput::make('name')
						->live()
						->afterStateUpdated(function ( Get $get, Set $set, ?string $old, ?string $state ) {
							if ( ($get('slug') ?? '') !== Str::slug($old) ) {
								return;
							}

							$set('slug', Str::slug($state));
						})->required(),

					TextInput::make('slug')->required()
				])
			]);
	}

	public static function table( Table $table ): Table
	{
		return $table
			->columns([
				TextColumn::make('id')->sortable(),
				TextColumn::make('name')->sortable(),
				TextColumn::make('slug')
			])
			->filters([
				//
			])
			->actions([
				Tables\Actions\EditAction::make(),
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
			//
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
