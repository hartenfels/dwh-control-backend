<?php

namespace App\EtlMonitor\Api\Http\Transformers;

use App\EtlMonitor\Common\Models\ElasticsearchModel;
use App\EtlMonitor\Common\Models\Interfaces\ModelInterface;
use App\EtlMonitor\Common\Models\Model;
use Illuminate\Database\Eloquent\Collection;
use ReflectionException;

class GenericTransformer extends Transformer
{

    /**
     * @param Model|ElasticsearchModel $model
     * @return array
     * @throws ReflectionException
     */
    public function transform(Model|ElasticsearchModel $model): array
    {
        $arr = [
            'id' => $model->getId(),
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
                if ($model->$relation instanceof ModelInterface) {
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

        return $arr;
    }

    /**
     * @param Model|ElasticsearchModel $model
     * @return array
     */
    protected function transformTimestamps(Model|ElasticsearchModel $model): array
    {
        $arr = [
            'created_at' => $model->created_at?->format('c'),
            'updated_at' => $model->updated_at?->format('c')
        ];

        foreach ($model->getCasts() as $field => $type) {
            if ($type == 'timestamp') {
                $arr[$field] = $model->$field?->format('c');
            }
        }

        return $arr;
    }

}
