<?php

namespace App\DwhControl\Common\Traits;

use App\DwhControl\Common\Models\History;
use App\DwhControl\Common\Models\Model;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait HasHistoryTrait
{

    /**
     * @return MorphMany
     */
    public function history(): MorphMany
    {
        return $this->morphMany(History::class, 'belongsToModel');
    }

    /**
     * @param string $event
     * @param array $params
     * @param CarbonInterface|null $date
     */
    public function writeHistory(string $event, array $params = [], CarbonInterface $date = null): void
    {
        $date = $date ?? Carbon::now();

        $this->history()->create([
            'event' => $event,
            'params' => implode('||', $this->parseParams($params)),
            'created_at' => $date->toDateTimeString()
        ]);
    }

    /**
     * @param array $params
     * @return array
     */
    protected function parseParams(array $params): array
    {
        return array_map(function ($param) {
            if (is_a($param, Model::class)) {
                return get_class($param) . '::' . $param->id;
            }

            return $param;
        }, $params);
    }

}
