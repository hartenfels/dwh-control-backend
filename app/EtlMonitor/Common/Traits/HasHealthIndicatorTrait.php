<?php

namespace App\EtlMonitor\Common\Traits;

use App\EtlMonitor\Common\Enum\HealthIndicatorStatusEnum;
use App\EtlMonitor\Common\Models\HealthIndicator;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasHealthIndicatorTrait
{

    /**
     * @return MorphMany
     */
    public function health_indicators(): MorphMany
    {
        return $this->morphMany(HealthIndicator::class, 'belongsToModel');
    }

    /**
     * @param int $type_id
     * @param string $name
     * @return HealthIndicator|null
     */
    public function health_indicator(int $type_id, string $name): ?HealthIndicator
    {
        /** @var HealthIndicator|null $indicator */
        $indicator = $this->health_indicators()->where('health_indicator_type_id', $type_id)->where('name', $name)->first();
        return $indicator;
    }

    /**
     * @param int $type_id
     * @param string $name
     * @param HealthIndicatorStatusEnum $status
     * @param string $status_text
     * @param float|null $value
     * @return HealthIndicator
     */
    public function setHealthIndicator(int $type_id, string $name, HealthIndicatorStatusEnum $status, string $status_text = '', float $value = null): HealthIndicator
    {
        if (is_null($indicator = $this->health_indicator($type_id, $name))) {
            $indicator = $this->health_indicators()->create([
                'health_indicator_type_id' => $type_id,
                'name' => $name,
                'status' => $status,
                'status_text' => $status_text,
                'value' => $value
            ]);
        } else {
            $indicator->update([
                'status' => $status,
                'status_text' => $status_text,
                'value' => $value
            ]);
        }

        $this->updateOverallHealth();

        return $indicator;
    }

    /**
     * @return $this
     */
    public function updateOverallHealth(): self
    {
        $order = HealthIndicatorStatusEnum::orderedByCriticality();
        $worst = HealthIndicatorStatusEnum::HEALTH_OK();

        $this->health_indicators->each(function (HealthIndicator $indicator) use ($order, &$worst) {
            if (array_search($indicator->status, $order) > $worst) $worst = $indicator->status;
        });

        $this->health = $worst;

        return $this;
    }

}
