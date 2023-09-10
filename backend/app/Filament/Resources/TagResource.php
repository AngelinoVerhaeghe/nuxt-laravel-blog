<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use App\Filament\Resources\TagResource\RelationManagers;
use App\Models\Tag;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TagResource extends Resource
{
	protected static ?string $model = Tag::class;

	protected static ?string $navigationIcon = 'heroicon-o-tag';

	public static function form( Form $form ): Form
	{
		return $form
			->schema([
				Section::make()->schema([
					TextInput::make('name')
						->live()
						->afterStateUpdated(function ( Get $get, Set $set, ?string $old, ?string $state ) {
							if ( ($get('slug') ?? '') !== Str::slug($old) ) {
								return;
							}

							$set('slug', Str::slug($state));
						})->required(),

					TextInput::make('slug')
						->required()
						->disabled(),
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
				TextColumn::make('articles_count')->counts('articles')
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
			//
		];
	}

	public static function getPages(): array
	{
		return [
			'index' => Pages\ListTags::route('/'),
			'create' => Pages\CreateTag::route('/create'),
			'edit' => Pages\EditTag::route('/{record}/edit'),
		];
	}
}
