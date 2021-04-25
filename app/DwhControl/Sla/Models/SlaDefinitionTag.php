<?php

namespace App\DwhControl\Sla\Models;

use App\DwhControl\Common\Attributes\PivotAttributeNames;
use App\DwhControl\Common\Attributes\PivotModelName;
use App\DwhControl\Common\Models\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SlaDefinitionTag extends Model
{

    protected $fillable = [
        'name', 'color', 'icon', 'hide_name'
    ];

    /**
     * @return BelongsToMany
     */
    #[PivotModelName('SlaDefinitionTagPivot')]
    #[PivotAttributeNames('tag_id', 'sla_definition_id')]
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(SlaDefinition::class, 'dwh_control_sla__sla_definition_tags__tags_pivot', 'tag_id', 'sla_definition_id');
    }

}
