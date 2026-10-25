<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
	protected static ?string $model = User::class;

	protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

	public static function form( Schema $schema ): Schema
	{
		return $schema
			->components([
				Section::make('Information')
					->schema([
						TextInput::make('name')
							->maxLength(255)
							->helperText('Volledige naam invullen')
							->required(),
						TextInput::make('email')
							->email()
							->required()
							->unique(ignoreRecord: true)
							->helperText(fn( string $operation ): ?string => $operation === 'create' ? 'An invitation to set a password is sent to this address.' : null),
						Select::make('roles')
							->multiple()
							->relationship('roles', 'name')
							->preload()
							->required()
							->helperText('admin: everything. editor: blog content (posts, articles, tags, categories).'),
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
				TextColumn::make('roles.name')
					->label(__('Roles'))
					->badge(),
				TextColumn::make('email_verified_at')
					->label(__('Status'))
					->badge()
					->state(fn( User $record ): string => $record->hasPendingInvitation() ? 'Invited' : 'Active')
					->color(fn( string $state ): string => $state === 'Active' ? 'success' : 'warning'),
			])
			->filters([
				//
			])
			->recordActions([
				ActionGroup::make([
					ViewAction::make(),
					EditAction::make(),
					Action::make('resendInvitation')
						->label(__('Resend invitation'))
						->icon('heroicon-o-envelope')
						->visible(fn( User $record ): bool => $record->hasPendingInvitation())
						->requiresConfirmation()
						->action(function ( User $record ): void {
							$record->sendInvitation();

							Notification::make()
								->success()
								->title(__('Invitation sent to :email', ['email' => $record->email]))
								->send();
						}),
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
			'index' => ListUsers::route('/'),
			'create' => CreateUser::route('/create'),
			'edit' => EditUser::route('/{record}/edit'),
		];
	}
}
