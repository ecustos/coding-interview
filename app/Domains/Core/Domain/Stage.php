<?php

namespace App\Domains\Core\Domain;

use Database\Factories\StageFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Stage extends Model
{
    /** @use HasFactory<StageFactory> */
    use HasFactory;

    protected static function newFactory(): StageFactory
    {
        return StageFactory::new();
    }

    protected $fillable = [
        'description',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    public function component(): HasOne
    {
        return $this->hasOne(BudgetComponent::class, 'id', 'id')
            ->where('type', BudgetComponent::TYPE_STAGE);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getTotal(): float
    {
        return (float) $this->total;
    }

    public function setTotal(float $total): self
    {
        $this->total = $total;

        return $this;
    }
}
