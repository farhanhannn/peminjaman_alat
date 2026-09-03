<?php
namespace App\Filament\Resources;
use App\Models\Category;
use Filament\Forms\Components\{TextInput, Textarea};
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Actions\{CreateAction, DeleteAction, DeleteBulkAction, EditAction};
use App\Filament\Resources\CategoryResource\Pages\{CreateCategory, EditCategory, ListCategories};
class CategoryResource extends Resource {
    protected static ?string $model=Category::class; protected static ?string $navigationLabel='Kategori Alat'; protected static ?string $modelLabel='Kategori'; protected static ?string $pluralModelLabel='Kategori Alat'; protected static string|\UnitEnum|null $navigationGroup='Master Data'; protected static ?int $navigationSort=2;
    public static function form(Schema $schema): Schema { return $schema->components([TextInput::make('name')->label('Nama Kategori')->required()->unique(ignoreRecord:true), Textarea::make('description')->label('Deskripsi')->rows(3)]); }
    public static function table(Table $table): Table { return $table->columns([TextColumn::make('name')->label('Kategori')->searchable()->sortable(),TextColumn::make('description')->label('Deskripsi')->limit(50),TextColumn::make('tools_count')->label('Jumlah Alat')])->actions([EditAction::make(),DeleteAction::make()])->bulkActions([DeleteBulkAction::make()]); }
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder { return parent::getEloquentQuery()->withCount('tools'); }
    public static function canAccess(): bool { return auth()->user()?->isAdmin() ?? false; }
    public static function getPages(): array { return ['index'=>ListCategories::route('/'),'create'=>CreateCategory::route('/create'),'edit'=>EditCategory::route('/{record}/edit')]; }
}
