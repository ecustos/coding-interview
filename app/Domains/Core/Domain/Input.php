<?php

namespace App\Domains\Core\Domain;

use Database\Factories\InputFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Input extends Model
{
    /** @use HasFactory<InputFactory> */
    use HasFactory;

    protected $fillable = [
        'description',
        'unit_price',
    ];

    protected static function newFactory(): InputFactory
    {
        return InputFactory::new();
    }

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
        ];
    }

    public function budgetComponents(): HasMany
    {
        return $this->hasMany(InputBudgetComponent::class);
    }
}
