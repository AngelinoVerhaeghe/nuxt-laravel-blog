<?php

namespace App\Filament\Resources;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\CreateAction;
use App\Filament\Resources\PostResource\Pages\ListPosts;
use App\Filament\Resources\PostResource\Pages\CreatePost;
use App\Filament\Resources\PostResource\Pages\EditPost;
use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PostResource extends Resource
{
	protected static ?string $model = Post::class;

	protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rocket-launch';

	//protected static ?string $label = 'Blogs';

	public static function form( Schema $schema ): Schema
	{
		return $schema
			->components([
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
				IconColumn::make('is_published')->label(__('Published'))->boolean()
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
			'index' => ListPosts::route('/'),
			'create' => CreatePost::route('/create'),
			'edit' => EditPost::route('/{record}/edit'),
		];
	}
}
