<?php
namespace App\Filament\Resources\LoanResource\Pages;
use App\Filament\Resources\LoanResource; use App\Services\LoanService; use Filament\Resources\Pages\CreateRecord;
class CreateLoan extends CreateRecord { protected static string $resource=LoanResource::class; protected function afterCreate():void { LoanService::log('peminjaman','create',"Pengajuan {$this->record->loan_code} dibuat."); } }
