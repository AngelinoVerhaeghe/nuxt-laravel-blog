<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\Filament\Resources\TagResource\Pages\ListTags;
use App\Filament\Resources\TagResource\Pages\CreateTag;
use App\Filament\Resources\TagResource\Pages\EditTag;
use App\Filament\Resources\TagResource\Pages;
use App\Filament\Resources\TagResource\RelationManagers;
use App\Models\Tag;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class TagResource extends Resource
{
	protected static ?string $model = Tag::class;

	protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-tag';

	public static function form( Schema $schema ): Schema
	{
		return $schema
			->components([
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
			//
		];
	}

	public static function getPages(): array
	{
		return [
			'index' => ListTags::route('/'),
			'create' => CreateTag::route('/create'),
			'edit' => EditTag::route('/{record}/edit'),
		];
	}
}
