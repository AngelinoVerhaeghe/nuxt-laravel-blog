<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
	protected static ?string $model = User::class;

	protected static ?string $navigationIcon = 'heroicon-o-users';

	public static function form( Form $form ): Form
	{
		return $form
			->schema([
				Section::make('Information')
					->schema([
						TextInput::make('name')
							->maxLength(255)
							->helperText('Volledige naam invullen')
							->required(),
						TextInput::make('email')->email()->required(),
						Select::make('roles')->multiple()->relationship('roles', 'name')
					])
			]);
	}

	public static function table( Table $table ): Table
	{
		return $table
			->columns([
				TextColumn::make('name')
					->label(__('Naam'))
					->sortable()
					->searchable(),
				TextColumn::make('email')
					->searchable(),
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
			'index' => Pages\ListUsers::route('/'),
			'create' => Pages\CreateUser::route('/create'),
			'edit' => Pages\EditUser::route('/{record}/edit'),
		];
	}
}
