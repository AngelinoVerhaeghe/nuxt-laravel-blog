<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
	protected static ?string $model = Post::class;

	protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

	//protected static ?string $label = 'Blogs';

	public static function form( Form $form ): Form
	{
		return $form
			->schema([
				Group::make()
					->schema([
						Section::make('Informatie')
							->schema([
								TextInput::make('title')
									->live(onBlur: true)
									->afterStateUpdated(fn( Set $set, ?string $state ) => $set('slug', Str::slug($state)))
									->required(),
								TextInput::make('slug')
									->required(),
								MarkdownEditor::make('description')
							])
					]),
				Group::make()
					->schema([
						Section::make()
							->schema([
								Select::make('user_id')
									->relationship(name: 'author', titleAttribute: 'name')
									->options(User::all()->pluck('name', 'id'))
									->searchable(),
								Select::make('category_id')
									->relationship(name: 'category', titleAttribute: 'name')
									->options(Category::all()->pluck('name', 'id'))
									->searchable()
							]),
						Section::make()
							->schema([
								SpatieMediaLibraryFileUpload::make('thumbnail')->collection('posts'),
								Toggle::make('is_published')->label(__('Active'))
							])
					]),
			]);

	}

	public static function table( Table $table ): Table
	{
		return $table
			->columns([
				TextColumn::make('title')->sortable(),
				SpatieMediaLibraryImageColumn::make('thumbnail')->collection('posts'),
				TextColumn::make('created_at')
					->date()
					->sortable(),
				BooleanColumn::make('is_published')->label(__('Published'))
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
			'index' => Pages\ListPosts::route('/'),
			'create' => Pages\CreatePost::route('/create'),
			'edit' => Pages\EditPost::route('/{record}/edit'),
		];
	}
}
