<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
	            Card::make()->schema([
		            Select::make('user_id')
			            ->relationship(name: 'author', titleAttribute: 'name')
			            ->options(User::all()->pluck('name', 'id'))
			            ->searchable(),
		            Select::make('category_id')
			            ->relationship(name: 'category', titleAttribute: 'name')
			            ->options(Category::all()->pluck('name', 'id'))
			            ->searchable(),
		            TextInput::make('title')
			            ->live()
			            ->afterStateUpdated(function ( Get $get, Set $set, ?string $old, ?string $state ) {
				            if ( ($get('slug') ?? '') !== Str::slug($old) ) {
					            return;
				            }

				            $set('slug', Str::slug($state));
			            })->required(),

		            TextInput::make('slug')->required(),
					SpatieMediaLibraryFileUpload::make('thumbnail')->collection('posts'),
		            RichEditor::make('description'),
		            Toggle::make('is_published')->label(__('Published'))
	            ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
	            TextColumn::make('id')->sortable(),
	            TextColumn::make('title')->sortable(),
				SpatieMediaLibraryImageColumn::make('thumbnail')->collection('posts'),
	            BooleanColumn::make('is_published')->label(__('Published'))
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }    
}