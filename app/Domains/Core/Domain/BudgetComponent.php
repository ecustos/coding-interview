<?php

namespace App\Domains\Core\Domain;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetComponent extends Model
{
    public const TYPE_STAGE = 'stage';

    public const TYPE_COMPOSITION = 'composition';

    public const TYPE_INPUT = 'input';

    public const COMPOSITION_OFFSET = 1_000_000;

    public const INPUT_OFFSET = 2_000_000;

    public $incrementing = false;

    protected $fillable = [
        'id',
        'description',
        'type',
        'budget_id',
        'composition_id',
        'input_id',
        'parent_stage_id',
        'total',
    ];

    protected function casts(): array
    {
        return [
            'total' => 'decimal:2',
        ];
    }

    public static function idForStage(Stage $stage): int
    {
        return (int) $stage->id;
    }

    public static function idForComposition(Composition $composition): int
    {
        return self::COMPOSITION_OFFSET + (int) $composition->id;
    }

    public static function idForInput(Input $input): int
    {
        return self::INPUT_OFFSET + (int) $input->id;
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

    public function parentStage(): BelongsTo
    {
        return $this->belongsTo(Stage::class, 'parent_stage_id');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getParentStageId(): ?int
    {
        return $this->parent_stage_id;
    }
}
