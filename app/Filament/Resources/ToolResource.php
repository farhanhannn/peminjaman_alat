<?php

namespace App\Filament\Resources;

use App\Models\{Category, Tool};
use App\Filament\Resources\ToolResource\Pages\{CreateTool, EditTool, ListTools};
use Filament\Forms\Components\{Select, TextInput, Textarea, Toggle};
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\{CreateAction, DeleteAction, DeleteBulkAction, EditAction};

class ToolResource extends Resource
{
    protected static ?string $model = Tool::class;
    protected static ?string $navigationLabel = 'Data Alat';
    protected static ?string $modelLabel = 'Alat';
    protected static ?string $pluralModelLabel = 'Data Alat';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('category_id')->label('Kategori')->options(Category::orderBy('name')->pluck('name', 'id'))->searchable()->required(),
            TextInput::make('code')->label('Kode Alat')->required()->unique(ignoreRecord: true)->maxLength(50),
            TextInput::make('name')->label('Nama Alat')->required()->maxLength(255),
            Textarea::make('description')->label('Deskripsi')->rows(3),
            TextInput::make('stock')->label('Stok')->numeric()->integer()->minValue(0)->required(),
            TextInput::make('available_stock')->label('Stok Tersedia')->numeric()->integer()->minValue(0)->required(),
            Select::make('condition')->label('Kondisi')->options(['baik'=>'Baik','rusak_ringan'=>'Rusak Ringan','rusak'=>'Rusak'])->required(),
            TextInput::make('daily_fine')->label('Denda per Hari')->numeric()->prefix('Rp')->required(),
            Toggle::make('is_active')->label('Aktif')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('code')->label('Kode')->searchable()->sortable(),
            TextColumn::make('name')->label('Nama Alat')->searchable()->sortable(),
            TextColumn::make('category.name')->label('Kategori')->sortable(),
            TextColumn::make('stock')->label('Stok'),
            TextColumn::make('available_stock')->label('Tersedia')->badge(),
            TextColumn::make('condition')->label('Kondisi')->badge(),
            TextColumn::make('daily_fine')->label('Denda/Hari')->money('IDR'),
            TextColumn::make('is_active')->label('Status')->badge()->formatStateUsing(fn ($state) => $state ? 'Aktif' : 'Nonaktif'),
        ])->actions([EditAction::make()->visible(fn () => auth()->user()?->isAdmin()), DeleteAction::make()->visible(fn () => auth()->user()?->isAdmin())])
          ->bulkActions([DeleteBulkAction::make()->visible(fn () => auth()->user()?->isAdmin())])
          ->defaultSort('name');
    }

    public static function canCreate(): bool { return auth()->user()?->isAdmin() ?? false; }
    public static function canAccess(): bool { return auth()->check() && in_array(auth()->user()->role, ['admin','petugas','peminjam'], true); }
    public static function getPages(): array { return ['index'=>ListTools::route('/'),'create'=>CreateTool::route('/create'),'edit'=>EditTool::route('/{record}/edit')]; }
}
