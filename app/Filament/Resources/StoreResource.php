<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StoreResource\Pages;
use App\Filament\Resources\StoreResource\RelationManagers;
use App\Filament\Resources\RichText;
use App\Models\Store;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StoreResource extends Resource
{
    protected static ?string $model = Store::class;

    // a função abaixo adiciona icon no menu lateral
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    //a função abaixo coloca na página do recurso uma busca global
    protected static ?string $recordTitleAttribute = 'name';

    //a função abaixo agrupa links da no menu lateral
    protected static ?string $navigationGroup = 'Admin';

    //a função abaixo muda o nome do link do menu laretal
    protected static ?string $navigationLabel = 'Lojas';

    //a função abaixo muda a ordem do link do menu laretal
    protected static ?int $navigationSort = 2;



    public static function form(Form $form): Form
    {
        return $form->columns(1)
            ->schema([

                TextInput::make('name')->required(),
                TextInput::make('phone')->required(),
                RichEditor::make('about')->required(),
                FileUpload::make('logo')
                ->image()
                ->directory('stores')
                ->imageResizeMode('cover')
                ->imageCropAspectRatio('16:9')
                ->imageResizeTargetWidth('900')
                ->imageResizeTargetHeight('506')
                ->disk('public'),
                TextInput::make('slug')->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->sortable()->searchable('Loja'),
                TextColumn::make('created_at')->date('d/m/Y H:i')->sortable(),
                TextColumn::make('updated_at')->date('d/m/Y H:i')->sortable(),
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
            'index' => Pages\ListStores::route('/'),
            'create' => Pages\CreateStore::route('/create'),
            'edit' => Pages\EditStore::route('/{record}/edit'),
        ];
    }

    //essa função abaixo coloca ao lado do icom a quantidade dele no bd
    public static function getNavigationBadge(): ?string
    {
        return self::getModel()::count();
    }
}
