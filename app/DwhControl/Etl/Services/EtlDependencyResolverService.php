<?php

namespace App\DwhControl\Etl\Services;

use App\DwhControl\Common\Services\Service;
use App\DwhControl\Etl\Models\EtlDefinition;
use Illuminate\Support\Collection;

class EtlDependencyResolverService extends Service
{

    /**
     * @var Collection
     */
    protected Collection $tree;

    /**
     * @var Collection
     */
    protected Collection $tree_flat;

    /**
     * EtlDependencyResolverService constructor.
     * @param EtlDefinition $model
     * @param int $depth
     */
    public function __construct(
        protected EtlDefinition $model,
        protected int $depth
    ) {}

    /**
     * @return array
     */
    public function __invoke(): array
    {
        $this->tree = new Collection();
        $this->tree_flat = new Collection();

        $this->resolveDependencies($this->model);

        return [$this->tree, $this->tree_flat];
    }

    /**
     * @param EtlDefinition $etlDefinition
     * @param int $depth
     */
    public function resolveDependencies(EtlDefinition $etlDefinition, int $depth = 0): void
    {
        if ($depth > $this->depth) return;

        $dependencies = $etlDefinition->depends_on;

        $dependencies->each(function (EtlDefinition $d) use ($depth) {
            $this->tree_flat->push($d);
            $this->resolveDependencies($d, $depth + 1);
        });
    }
}
