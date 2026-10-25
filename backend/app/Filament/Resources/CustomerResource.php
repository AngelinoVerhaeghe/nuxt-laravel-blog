<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\Filament\Resources\CustomerResource\Pages\ListCustomers;
use App\Filament\Resources\CustomerResource\Pages\CreateCustomer;
use App\Filament\Resources\CustomerResource\Pages\EditCustomer;
use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CustomerResource extends Resource
{
	protected static ?string $model = Customer::class;

	protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-user-group';

	protected static ?int $navigationSort = 2;

	protected static string | \UnitEnum | null $navigationGroup = 'Shop';

	public static function form( Schema $schema ): Schema
	{
		return $schema
			->components([
				Section::make()
					->schema([
						TextInput::make('name')
							->maxValue(50)
							->required(),
						TextInput::make("email")
							->label('Email Address')
							->required()
							->email()
							->unique(ignoreRecord: true),
						TextInput::make('phone')
							->maxValue(50),
						DatePicker::make('date_of_birth'),
						TextInput::make('zip_code')
							->required(),
						TextInput::make('city')
							->required(),
						TextInput::make('address')
							->required()
							->columnSpan('full'),
						TextInput::make('country')
							->required(),
					])->columns(2)
			]);
	}

	public static function table( Table $table ): Table
	{
		return $table
			->columns([
				TextColumn::make('name')
					->sortable()
					->searchable(),
				TextColumn::make('email')
					->sortable()
					->searchable(),
				TextColumn::make('phone')
					->sortable()
					->searchable(),
				TextColumn::make('city')
					->sortable()
					->searchable(),
				TextColumn::make('date_of_birth')
					->date()
					->sortable()
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
			'index' => ListCustomers::route('/'),
			'create' => CreateCustomer::route('/create'),
			'edit' => EditCustomer::route('/{record}/edit'),
		];
	}
}
