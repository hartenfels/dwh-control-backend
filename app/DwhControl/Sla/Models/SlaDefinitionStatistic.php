<?php

namespace App\DwhControl\Sla\Models;

use App\DwhControl\Common\Models\Model;
use App\DwhControl\Sla\Models\Interfaces\SlaInterface;
use App\DwhControl\Sla\Traits\SlaTypes;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use MathPHP\Exception\BadDataException;
use MathPHP\Exception\OutOfBoundsException;
use MathPHP\Statistics\Descriptive;

class SlaDefinitionStatistic extends Model
{

    use SlaTypes;

    /**
     * @var string[]
     */
    protected $fillable = [
        'sla_definition_id', 'type', 'achievement_history'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'achievement_history' => 'array',
    ];

    /**
     * @return $this
     */
    public function calculate(): self
    {
        $this->calculateHistory();

        return $this;
    }

    /**
     * @param CarbonInterface|null $day
     * @param int $days
     * @param bool $save
     * @return $this
     */
    public function calculateHistory(CarbonInterface $day = null, int $days = 28, bool $save = true): self
    {
        $day = $day ?? Carbon::now();
        /** @var Collection<SlaInterface> $slas */
        $slas = $this->definition->slas()
            ->where('range_start', '<', $day)
            ->where('range_start', '>=', (clone $day)->subDays($days)->startOfDay())
            ->orderBy('range_start', 'desc')
            ->get();

        $this->achievement_history = $slas->reverse()->map(function (SlaInterface $sla) {
            return [
                'sla_id' => $sla->id,
                'status' => $sla->status,
                'day' => $sla->range_end
            ];
        })->values()->sortBy('day');

        if ($save) $this->save();

        return $this;
    }

    /**
     * @return BelongsTo
     */
    public function definition(): BelongsTo
    {
        return $this->belongsTo(static::sla_types()->{$this->type}->definition, 'sla_definition_id');
    }

}
