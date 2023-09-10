<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

	protected static ?string $navigationGroup = 'Shop';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()
	            ->schema([
					Section::make()
		            ->schema([
						TextInput::make('name'),
						TextInput::make('slug'),
			            MarkdownEditor::make("description")->columnSpan('full')
		            ])->columns(2),
		            Section::make('Pricing & Inventory')
			            ->schema([
				            TextInput::make('sku'),
				            TextInput::make('price'),
				            TextInput::make('quantity'),
			            ])->columns(3)
	            ]),
	            Group::make()
		            ->schema([
			            Section::make('Status')
				            ->schema([
								Toggle::make('is_visible'),
								Toggle::make('is_featured'),
								DatePicker::make('published_at')
				            ]),
			            Section::make('Image')
				            ->schema([
					            FileUpload::make('image')
						            ->directory('pops')
						            ->preserveFilenames()
				            ]),
			            Section::make('Associations')
				            ->schema([
					           Select::make('brand_id')->relationship('brand', 'name')
				            ])
		            ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
				Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('brand.name'),
	            Tables\Columns\IconColumn::make('is_visible')->boolean(),
	            Tables\Columns\TextColumn::make('price'),
	            Tables\Columns\TextColumn::make('quantity'),
	            Tables\Columns\TextColumn::make('published_at'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }    
}
