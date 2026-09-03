<?php
namespace App\Filament\Resources;
use App\Filament\Resources\ActivityLogResource\Pages\ListActivityLogs;
use App\Models\ActivityLog;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
class ActivityLogResource extends Resource {
    protected static ?string $model=ActivityLog::class; protected static ?string $navigationLabel='Log Aktifitas'; protected static ?string $modelLabel='Log Aktifitas'; protected static ?string $pluralModelLabel='Log Aktifitas'; protected static string|\UnitEnum|null $navigationGroup='Monitoring'; protected static ?int $navigationSort=2;
    public static function form(Schema $schema):Schema{return $schema;}
    public static function table(Table $table):Table{return $table->columns([TextColumn::make('created_at')->label('Waktu')->dateTime('d/m/Y H:i:s')->sortable(),TextColumn::make('user.name')->label('User')->searchable(),TextColumn::make('module')->label('Modul')->badge(),TextColumn::make('action')->label('Aksi')->badge(),TextColumn::make('description')->label('Deskripsi')->wrap(),TextColumn::make('ip_address')->label('IP')])->defaultSort('created_at','desc')->paginated([25,50,100]);}
    public static function canAccess():bool{return auth()->user()?->isAdmin() ?? false;}
    public static function getPages():array{return ['index'=>ListActivityLogs::route('/')];}
}
