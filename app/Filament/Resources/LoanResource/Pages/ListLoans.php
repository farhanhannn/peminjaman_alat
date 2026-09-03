<?php
namespace App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource; use Filament\Actions\CreateAction; use Filament\Resources\Pages\ListRecords;
class ListLoans extends ListRecords { protected static string $resource=LoanResource::class; protected function getHeaderActions():array{return [CreateAction::make()->label('Ajukan Peminjaman')->visible(fn()=>auth()->user()?->isAdmin() || auth()->user()?->isPeminjam())];} }
