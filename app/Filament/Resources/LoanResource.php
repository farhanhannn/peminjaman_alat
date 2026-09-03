<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LoanResource\Pages\{CreateLoan, EditLoan, ListLoans};
use App\Models\{Loan, Tool, User};
use App\Services\LoanService;
use Filament\Actions\Action;
use Filament\Forms\Components\{DatePicker, Hidden, Select, TextInput, Textarea};
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Notification;

class LoanResource extends Resource
{
    protected static ?string $model = Loan::class;
    protected static ?string $navigationLabel = 'Peminjaman';
    protected static ?string $modelLabel = 'Peminjaman';
    protected static ?string $pluralModelLabel = 'Peminjaman';
    protected static string|\UnitEnum|null $navigationGroup = 'Transaksi';
    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        $isAdmin = auth()->user()?->isAdmin() ?? false;
        return $schema->components([
            TextInput::make('loan_code')->label('Kode Peminjaman')->default(fn () => 'PJM-' . now()->format('YmdHis') . '-' . random_int(10, 99))->required()->unique(ignoreRecord: true)->disabledOn('edit'),
            $isAdmin
                ? Select::make('borrower_id')->label('Peminjam')->options(User::where('role','peminjam')->where('is_active',true)->orderBy('name')->pluck('name','id'))->searchable()->required()
                : Hidden::make('borrower_id')->default(fn () => auth()->id()),
            Select::make('tool_id')->label('Alat')->options(function () {
                $query = Tool::query()->where('is_active', true);
                if (! auth()->user()?->isAdmin()) $query->where('available_stock', '>', 0);
                return $query->orderBy('name')->get()->mapWithKeys(fn ($tool) => [$tool->id => $tool->name . ' (' . $tool->available_stock . ' tersedia)']);
            })->searchable()->required(),
            TextInput::make('quantity')->label('Jumlah')->numeric()->integer()->minValue(1)->default(1)->required(),
            DatePicker::make('loan_date')->label('Tanggal Pinjam')->default(now())->required(),
            DatePicker::make('due_date')->label('Batas Pengembalian')->minDate(now())->required(),
            $isAdmin
                ? Select::make('status')->label('Status')->options(['pending'=>'Menunggu','borrowed'=>'Dipinjam','returned'=>'Dikembalikan','rejected'=>'Ditolak','overdue'=>'Terlambat'])->required()
                : Hidden::make('status')->default('pending'),
            Textarea::make('notes')->label('Catatan')->rows(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('loan_code')->label('Kode')->searchable()->sortable(),
            TextColumn::make('borrower.name')->label('Peminjam')->searchable()->sortable(),
            TextColumn::make('tool.name')->label('Alat')->searchable(),
            TextColumn::make('quantity')->label('Qty'),
            TextColumn::make('loan_date')->label('Pinjam')->date('d/m/Y')->sortable(),
            TextColumn::make('due_date')->label('Jatuh Tempo')->date('d/m/Y')->sortable(),
            TextColumn::make('return_date')->label('Kembali')->date('d/m/Y'),
            TextColumn::make('status')->label('Status')->badge()->formatStateUsing(fn ($state) => match ($state) { 'pending'=>'Menunggu','borrowed'=>'Dipinjam','returned'=>'Dikembalikan','rejected'=>'Ditolak','overdue'=>'Terlambat', default=>$state }),
            TextColumn::make('fine')->label('Denda')->money('IDR'),
        ])->filters([SelectFilter::make('status')->options(['pending'=>'Menunggu','borrowed'=>'Dipinjam','returned'=>'Dikembalikan','rejected'=>'Ditolak','overdue'=>'Terlambat'])])
          ->actions([
              Action::make('approve')->label('Setujui')->requiresConfirmation()->visible(fn (Loan $record) => auth()->user()?->isAdmin() || auth()->user()?->isPetugas() ? $record->status === 'pending' : false)->action(function (Loan $record) { LoanService::approve($record); Notification::make()->title('Peminjaman disetujui')->success()->send(); }),
              Action::make('reject')->label('Tolak')->color('danger')->requiresConfirmation()->visible(fn (Loan $record) => (auth()->user()?->isAdmin() || auth()->user()?->isPetugas()) && $record->status === 'pending')->action(function (Loan $record) { LoanService::reject($record); Notification::make()->title('Peminjaman ditolak')->warning()->send(); }),
              Action::make('return')->label('Kembalikan')->color('success')->requiresConfirmation()->visible(fn (Loan $record) => in_array($record->status, ['borrowed','overdue'], true) && (auth()->user()?->isAdmin() || auth()->user()?->isPetugas() || $record->borrower_id === auth()->id()))->action(function (Loan $record) { $loan = LoanService::return($record); Notification::make()->title('Alat berhasil dikembalikan')->body('Denda: Rp ' . number_format((float)$loan->fine, 0, ',', '.'))->success()->send(); }),
              Action::make('print')->label('Cetak')->icon('heroicon-o-printer')->url(fn () => route('reports.loans'))->openUrlInNewTab()->visible(fn () => auth()->user()?->isAdmin() || auth()->user()?->isPetugas()),
          ])->defaultSort('created_at','desc');
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()->with(['borrower','tool']);
        if (auth()->user()?->isPeminjam()) $query->where('borrower_id', auth()->id());
        return $query;
    }

    public static function canCreate(): bool { return auth()->check() && in_array(auth()->user()->role, ['admin','peminjam'], true); }
    public static function canAccess(): bool { return auth()->check(); }
    public static function canEdit($record): bool { return auth()->user()?->isAdmin() || (auth()->user()?->isPeminjam() && $record->borrower_id === auth()->id() && $record->status === 'pending'); }
    public static function canDelete($record): bool { return auth()->user()?->isAdmin() && $record->status === 'pending'; }
    public static function getPages(): array { return ['index'=>ListLoans::route('/'),'create'=>CreateLoan::route('/create'),'edit'=>EditLoan::route('/{record}/edit')]; }
}
