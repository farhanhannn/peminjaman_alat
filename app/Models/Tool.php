<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tool extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'code', 'name', 'description', 'stock', 'available_stock',
        'condition', 'daily_fine', 'is_active',
    ];

    protected function casts(): array
    {
        return ['stock' => 'integer', 'available_stock' => 'integer', 'daily_fine' => 'decimal:2', 'is_active' => 'boolean'];
    }

    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function loans(): HasMany { return $this->hasMany(Loan::class); }
}
