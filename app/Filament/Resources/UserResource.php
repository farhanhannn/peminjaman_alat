<?php

namespace App\Filament\Resources;

use App\Models\User;
use Filament\Forms\Components\{Select, TextInput, Toggle};
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\{TextColumn, ToggleColumn};
use Filament\Tables\Table;
use Filament\Actions\{DeleteAction, DeleteBulkAction, EditAction};
use App\Filament\Resources\UserResource\Pages\{CreateUser, EditUser, ListUsers};

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationLabel = 'User';
    protected static ?string $modelLabel = 'User';
    protected static ?string $pluralModelLabel = 'User';
    protected static string|\UnitEnum|null $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->label('Nama')->required()->maxLength(255),
            TextInput::make('email')->email()->required()->unique(ignoreRecord: true),
            Select::make('role')->label('Level Pengguna')->options(['admin'=>'Admin','petugas'=>'Petugas','peminjam'=>'Peminjam'])->required(),
            Toggle::make('is_active')->label('Aktif')->default(true),
            TextInput::make('password')->password()->revealable()->dehydrated(fn ($state) => filled($state))->required(fn (string $context): bool => $context === 'create')->minLength(8),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->label('Nama')->searchable()->sortable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('role')->label('Level')->badge(),
            ToggleColumn::make('is_active')->label('Aktif'),
            TextColumn::make('created_at')->label('Dibuat')->dateTime('d/m/Y H:i')->sortable(),
        ])->actions([EditAction::make(), DeleteAction::make()])
          ->bulkActions([DeleteBulkAction::make()])
          ->defaultSort('created_at', 'desc');
    }

    public static function canAccess(): bool { return auth()->user()?->isAdmin() ?? false; }
    public static function getPages(): array { return ['index'=>ListUsers::route('/'),'create'=>CreateUser::route('/create'),'edit'=>EditUser::route('/{record}/edit')]; }
}
