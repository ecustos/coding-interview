<?php

namespace App\Domains\Core\Domain;

use Database\Factories\BudgetComponentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetComponent extends Model
{
    /** @use HasFactory<BudgetComponentFactory> */
    use HasFactory;

    public const TYPE_STAGE = 'stage';

    public const TYPE_COMPOSITION = 'composition';

    public const TYPE_INPUT = 'input';

    protected static ?string $componentType = null;

    protected $table = 'budget_components';

    protected $fillable = [
        'description',
        'type',
        'budget_id',
        'composition_id',
        'input_id',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    protected static function newFactory(): Factory
    {
        return BudgetComponentFactory::new();
    }

    protected static function booted(): void
    {
        if (static::$componentType === null) {
            return;
        }

        static::addGlobalScope('component_type', function (Builder $builder): void {
            $builder->where('type', static::$componentType);
        });
    }

    public function newFromBuilder($attributes = [], $connection = null)
    {
        if (static::class !== self::class) {
            return parent::newFromBuilder($attributes, $connection);
        }

        $attributes = (array) $attributes;
        $modelClass = match ($attributes['type'] ?? null) {
            self::TYPE_STAGE => StageBudgetComponent::class,
            self::TYPE_COMPOSITION => CompositionBudgetComponent::class,
            self::TYPE_INPUT => InputBudgetComponent::class,
            default => static::class,
        };

        $model = (new $modelClass)->newInstance([], true);
        $model->setRawAttributes($attributes, true);
        $model->setConnection($connection ?? $this->getConnectionName());
        $model->fireModelEvent('retrieved', false);

        return $model;
    }

    public function budget(): BelongsTo
    {
        return $this->belongsTo(Budget::class);
    }

    public function composition(): BelongsTo
    {
        return $this->belongsTo(Composition::class);
    }

    public function input(): BelongsTo
    {
        return $this->belongsTo(Input::class);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setBudgetId(int $budgetId): self
    {
        $this->budget_id = $budgetId;

        return $this;
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
