<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use NumberFormatter;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    //a função abaixo coloca na página do recurso uma busca global
    protected static ?string $recordTitleAttribute = 'name';

    //a função abaixo agrupa links da no menu lateral
    protected static ?string $navigationGroup = 'Admin';

    //a função abaixo muda o nome do link do menu laretal
    protected static ?string $navigationLabel = 'Produtos';

    //a função abaixo muda a ordem do link do menu laretal
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form->columns(1)
            ->schema([
                TextInput::make('name')
                ->reactive()
                ->afterStateUpdated(function($state, $set){
                    $state = str()->of($state)->slug();
                    $set('slug', $state);
                })
                ->lazy() // Atualiza o slug somente quando o usuário sair do campo
                ->required(),
                Select::make('store_id')
                    ->relationship('store', 'name', fn(Builder $query)
                    => $query->whereRelation('tenant', 'tenant_id', '=', Filament::getTenant()->id))
                    ->required(),

                TextInput::make('description'),
                RichEditor::make('body')->required(),

                Section::make('Dados complementares')->columns(2)->schema([

                    TextInput::make('price')->required(),
                    Toggle::make('status')->required(),
                    TextInput::make('stock')->required(),
                    TextInput::make('slug')
                    ->required()
                    ->disabled(),

                ]),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->sortable()->searchable()->label('Produtos'),
                TextColumn::make('price')->formatStateUsing(function ($state) {
                    // Divide o valor em centavos para reais
                    $valueInReais = $state / 100;

                    // Formata o valor em reais com a moeda brasileira
                    $formatter = new NumberFormatter('pt_BR', NumberFormatter::CURRENCY);
                    return $formatter->format($valueInReais);
                })->label('Preço'),
                TextColumn::make('created_at')->date('d/m/Y H:i:s'),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }

    //essa função abaixo coloca ao lado do icom a quantidade dele no bd
    public static function getNavigationBadge(): ?string
    {
        return self::getModel()::count();
    }
}
