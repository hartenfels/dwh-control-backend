<?php

namespace App\EtlMonitor\Api\Http\Transformers;

use App\EtlMonitor\Common\Models\Model;
use Illuminate\Database\Eloquent\Collection;
use ReflectionException;

class GenericTransformer extends Transformer
{

    /**
     * @param Model $model
     * @return array
     * @throws ReflectionException
     */
    public function transform(Model $model): array
    {
        $arr = [
            'id' => (int)$model->id, //@TODO: Fix for SQLite
            '_icon' => $model->getIcon(),
            '_model' => $model::model(),
            '_entity' => $model->entity(),
            '_fk' => $model->fk(),
            '_self' => $model->self(),
            '_relations' => []
        ];

        foreach ($model->getTransformable() as $transformable) {
            if (isset($model->$transformable)) $arr[$transformable] = $model->$transformable;
        }

        $relations = [];
        foreach ($model::getRelationNames() as $relation => $return_type) {
            $arr['_relations'][$relation] = $return_type;
            if ($model->relationLoaded($relation)) {
                if ($model->$relation instanceof Model) {
                    $relations[$relation] = $model->$relation->enrich()->transform();
                } elseif ($model->$relation instanceof Collection) {
                    $relations[$relation] = $model->$relation->map(function ($m) {
                        return $m->enrich()->transform();
                    })->toArray();
                } else {
                    $relations[$relation] = null;
                }
            }
        }

        $arr['relations'] = $relations;
        $arr['timestamps'] = $this->transformTimestamps($model);

        foreach ($arr as $field => $value) { //@TODO: Fix for SQLite
            if (preg_match('/_id$/', $field)) {
                $arr[$field] = (int)$value;
            }
        }

        return $arr;
    }

    /**
     * @param Model $model
     * @return array
     */
    protected function transformTimestamps(Model $model): array
    {
        $arr = [
            'created_at' => $model->created_at->format('c'),
            'updated_at' => $model->updated_at->format('c')
        ];

        foreach ($model->getCasts() as $field => $type) {
            if ($type == 'timestamp') {
                $arr[$field] = $model->$field->format('c');
            }
        }

        return $arr;
    }

}
