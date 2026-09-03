<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Loan extends Model
{
    use HasFactory;

    protected $fillable = [
        'loan_code', 'borrower_id', 'tool_id', 'quantity', 'loan_date', 'due_date',
        'return_date', 'approved_by', 'returned_by', 'status', 'fine', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'loan_date' => 'date', 'due_date' => 'date', 'return_date' => 'date',
            'quantity' => 'integer', 'fine' => 'decimal:2',
        ];
    }

    public function borrower(): BelongsTo { return $this->belongsTo(User::class, 'borrower_id'); }
    public function tool(): BelongsTo { return $this->belongsTo(Tool::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }
    public function returner(): BelongsTo { return $this->belongsTo(User::class, 'returned_by'); }
}
