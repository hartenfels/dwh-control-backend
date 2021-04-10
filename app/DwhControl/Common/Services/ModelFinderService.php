<?php

namespace App\DwhControl\Common\Services;

use App\DwhControl\Common\Exceptions\ModelNotFoundException;
use App\DwhControl\Common\Models\Model;

class ModelFinderService extends Service
{

    /**
     * @param string $class
     * @param int $id
     * @param array $with
     * @return Model
     * @throws ModelNotFoundException
     */
    public static function findOrFail(string $class, int $id, array $with = []): Model
    {
        try {
            return $class::with($with)->findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $ex) {
            throw new ModelNotFoundException($class, $id);
        }
    }

    /**
     * @param string $class
     * @param int $id
     * @param array $with
     * @return Model|null
     */
    public static function find(string $class, int $id, array $with = []): ?Model
    {
        return $class::with($with)->find($id);
    }

    public function __invoke(): mixed
    {
        // TODO: Implement __invoke() method.
    }
}
