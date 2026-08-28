<?php

namespace App\Domains\Core\Domain;

use Database\Factories\CompositionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Composition extends Model
{
    /** @use HasFactory<CompositionFactory> */
    use HasFactory;

    protected $fillable = [
        'description',
        'total',
    ];

    protected static function newFactory(): CompositionFactory
    {
        return CompositionFactory::new();
    }

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    public function budgetComponents(): HasMany
    {
        return $this->hasMany(CompositionBudgetComponent::class);
    }
}
