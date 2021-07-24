<?php

namespace App\DwhControl\Sla\Models;

use App\DwhControl\Common\Attributes\PivotAttributeNames;
use App\DwhControl\Common\Attributes\PivotModelName;
use App\DwhControl\Etl\Models\EtlDefinition;
use App\DwhControl\Etl\Services\EtlDependencyResolverService;
use App\DwhControl\Sla\Models\Abstract\SlaDefinitionAbstract;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * App\DwhControl\Sla\Models\DeliverableSlaDefinition
 *
 * @property int $id
 * @property string $type
 * @property string $name
 * @property float|null $target_percent
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\DwhControl\Sla\Models\DailyTimerange[] $daily_timeranges
 * @property-read int|null $daily_timeranges_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\DwhControl\Common\Models\History[] $history
 * @property-read int|null $history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\DwhControl\Common\Models\Property[] $properties
 * @property-read int|null $properties_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\DwhControl\Sla\Models\DeliverableSla[] $slas
 * @property-read int|null $slas_count
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition query()
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition whereTargetPercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DeliverableSlaDefinition whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class DeliverableSlaDefinition extends SlaDefinitionAbstract
{

    /**
     * @var string[]
     */
    protected $attributes = [
        'type' => 'deliverable'
    ];

    /**
     * @var string
     */
    protected static string $type = 'deliverable';

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return 'mdi-truck-check-outline';
    }

    /**
     * @return BelongsToMany
     */
    #[PivotModelName('EtlDefinitionAffectedSlaPivot')]
    #[PivotAttributeNames('etl_definition_id', 'sla_definition_id')]
    public function affecting_etls(): BelongsToMany
    {
        return $this->belongsToMany(EtlDefinition::class, 'dwh_control_sla__sla_definition_affecting_etls', 'etl_definition_id', 'sla_definition_id');
    }

    /**
     * @return $this
     */
    public function calculateAffectingEtls(): self
    {
        if ($this->source != 'etl') {
            $this->affecting_etls()->sync([]);
            return $this;
        }

        $etl_ids = array_map(fn($rule) => $rule['etl_id'], $this->rules['etls']);
        $etl_definitions = EtlDefinition::query()->whereIn('etl_id', array_values($etl_ids))->get();

        $etl_definitions->each(function (EtlDefinition $d) use ($etl_definitions) {
            list($_, $flat) = EtlDependencyResolverService::make($d, config('dwh_control.sla_calculate_affecting_etls.depth', 5))->invoke();
            $flat->each(function (EtlDefinition $dep) use ($etl_definitions) {
                $etl_definitions->push($dep);
            });
        });

        $this->affecting_etls()->sync($etl_definitions->map(fn ($d) => $d->id)->toArray());

        $this->logDebug('Affecting ETLs calculated (Count: %s)', $etl_definitions->count());

        return $this;
    }
}
