<?php

namespace App\DwhControl\Api\Http\Controllers;

use App\DwhControl\Api\Attributes\CustomAction;
use App\DwhControl\Api\Exceptions\MissingRequestFieldException;
use App\DwhControl\Api\Exceptions\UnhandleableRelationshipException;
use App\DwhControl\Api\Http\Controllers\Actions\DestroyAction;
use App\DwhControl\Api\Http\Controllers\Actions\StoreAction;
use App\DwhControl\Api\Http\Controllers\Actions\UpdateAction;
use App\DwhControl\Api\Http\Requests\Request;
use App\DwhControl\Common\Attributes\PivotAttributeNames;
use App\DwhControl\Common\Attributes\PivotModelName;
use App\DwhControl\Common\Enum\HttpStatusCodeEnum;
use App\DwhControl\Common\Exceptions\ModelNotFoundException;
use App\DwhControl\Common\Models\ElasticsearchModel;
use App\DwhControl\Common\Models\Model;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Matchory\Elasticsearch\Query;
use Matthenning\EloquentApiFilter\Traits\FiltersEloquentApi;
use ReflectionClass;
use ReflectionException;

class Controller extends \App\Http\Controllers\Controller
{

    use FiltersEloquentApi;

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var array
     */
    protected array $meta = [];

    /**
     * @var string
     */
    protected string $package = 'Api';

    /**
     * Controller constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param array $arguments
     * @return Response|void
     * @throws AuthorizationException
     */
    public function auth($arguments = [])
    {
        $this->authorize($this->gate(), $arguments);
    }

    /**
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function _index(): JsonResponse
    {
        $this->auth();

        $model_class_name = $this->getModelName();
        $query = $model_class_name::query();

        if ($this->request->has('all')) {
            return $this->respondFiltered($query);
        }

        return $this->respondFilteredAndPaginated($query);
    }

    /**
     * @param int|string $id
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function _show(int|string $id): JsonResponse
    {
        $this->auth();

        $model_class_name = $this->getModelName();
        $query = $model_class_name::where('id', $id);

        return $this->respondFiltered($query);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param callable $pre Function to execute before model deletion. Parameters: Request $request
     * @param callable $post Function to execute after model deletion. Parameters: mixed $result_of_pre_function, Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     */
    public function _destroy(Request $request, int $id, callable $pre = null, callable $post = null): JsonResponse
    {
        $this->auth();

        $pre_result = $pre ? $pre($request) : null;

        DestroyAction::prepare($request, $this->getModelName(), $id)->auto()->invoke();

        if ($post) $post($pre_result, $request);

        return $this->respond();
    }

    /**
     * @param Request $request
     * @param $except
     * @param callable $pre Function to execute before model creation. Parameters: Request $request
     * @param callable $post Function to execute after model creation. Parameters: mixed $result_of_pre_function, Model $created_model, Request $request
     * @return JsonResponse
     * @throws MissingRequestFieldException
     * @throws UnhandleableRelationshipException
     * @throws ReflectionException
     * @throws AuthorizationException
     */
    public function _store(Request $request, $except = [], callable $pre = null, callable $post = null): JsonResponse
    {
        $this->auth();

        $pre_result = $pre ? $pre($request) : null;

        $model = StoreAction::prepare($request, $this->getModelName())->except($except)->auto()->invoke();

        if ($post) $post($pre_result, $model, $request);

        return $this->respondWithModel($model->fresh());
    }

    /**
     * @param Request $request
     * @param int $id
     * @param array $except
     * @param callable|null $pre Function to execute before model update. Parameters: Request $request
     * @param callable|null $post Function to execute after model update. Parameters: mixed $result_of_pre_function, Model $model, Request $request
     * @return JsonResponse
     * @throws AuthorizationException
     * @throws MissingRequestFieldException
     * @throws ReflectionException
     * @throws UnhandleableRelationshipException
     * @throws ModelNotFoundException
     */
    public function _update(Request $request, int $id, $except = [], callable $pre = null, callable $post = null): JsonResponse
    {
        $this->auth();

        $pre_result = $pre ? $pre($request) : null;

        $model = UpdateAction::prepare($request, $this->getModelName(), $id)->except($except)->auto()->invoke();

        if ($post) $post($pre_result, $model, $request);

        return $this->respondWithModel($model->fresh());
    }

    /**
     * @param EloquentBuilder|QueryBuilder|Query|Relation $query
     * @return JsonResponse
     */
    protected function respondFiltered(EloquentBuilder|QueryBuilder|Query|Relation $query): JsonResponse
    {
        $results = $this->filterApiRequest($this->request, $query)->get();

        return $this->respondWithModels($results);
    }

    /**
     * @param EloquentBuilder|QueryBuilder|Query|Relation $query
     * @param callable|null $model_retrieval_service
     * @return JsonResponse
     */
    protected function respondPaginated(EloquentBuilder|QueryBuilder|Query|Relation $query, callable $model_retrieval_service = null): JsonResponse
    {
        $results = $this->paginateModels($query);

        return $this->respondWithPaginatedModels($results, $model_retrieval_service);
    }

    /**
     * @param EloquentBuilder|QueryBuilder|Query|Relation $query
     * @param callable|null $model_retrieval_service
     * @return JsonResponse
     */
    protected function respondFilteredAndPaginated(EloquentBuilder|QueryBuilder|Query|Relation $query, callable $model_retrieval_service = null): JsonResponse
    {
        $query = $this->filterApiRequest($this->request, $query);
        $results = $this->paginateModels($query);

        return $this->respondWithPaginatedModels($results, $model_retrieval_service);
    }

    /**
     * @param EloquentBuilder|QueryBuilder|Query|Relation $query
     * @return LengthAwarePaginator
     */
    public function paginateModels(EloquentBuilder|QueryBuilder|Query|Relation $query): LengthAwarePaginator
    {
        if ($this->request->has('per_page')) {
            $perPage = ($pp = (int)$this->request->get('per_page')) == -1 ? $query->count() : $pp;
        } else {
            $perPage = config('pagination_per_page');
        }

        return $query->paginate($perPage);
    }

    /**
     * @param Model $model
     * @return JsonResponse
     */
    public function respondWithModel(Model $model): JsonResponse
    {
        return $this->respondWithModels(new Collection([$model]));
    }

    /**
     * @param Collection|array $model_transformed
     * @return Collection
     * @throws ReflectionException
     */
    protected function getSeparateModelsFromRelations(Collection|array $model_transformed): Collection
    {
        $models = new Collection();
        foreach ($model_transformed['relations'] as $name => $related_models) {
            if (is_null($related_models)) continue;

            if (count($related_models) > 0 && !isset($related_models['_model'])) {
                // Has Many related models -> $related_models contains an array of models
                foreach ($related_models as $m) {
                    if (isset($m['relations']) && count($m['relations']) > 0) {
                        $models = $models->merge($this->getSeparateModelsFromRelations($m));
                    }

                    $models->add($m);

                    $reflection_class = new ReflectionClass($model_transformed['_model_fqn']);
                    if (count($a = $reflection_class->getMethod($name)->getAttributes(PivotModelName::class)) > 0) {
                        $pivot_model = $a[0]->newInstance()->pivot_name;
                    } else {
                        $pivot_model = strcmp($model_transformed['_model'], $m['_model']) < 0 ?
                            $model_transformed['_model'] . $m['_model'] :
                            $m['_model'] . $model_transformed['_model'];
                        $pivot_model .= 'Pivot';
                    }

                    if (count($a = $reflection_class->getMethod($name)->getAttributes(PivotAttributeNames::class)) > 0) {
                        $pivot_key = $a[0]->newInstance()->key;
                        $pivot_foreign_key = $a[0]->newInstance()->foreign_key;
                    } else {
                        $pivot_key = $model_transformed['_fk'];
                        $pivot_foreign_key = $m['_fk'];
                    }

                    if ($model_transformed['_relations'][$name] == 'BelongsToMany') {
                        // Many to Many relation -> we need to generate intermediate Models
                        $models->add([
                            '_model' => $pivot_model,
                            '_meta' => ['is_virtual' => true],
                            'id' => '_pivot-' . $model_transformed['id'] . '-' . $m['_model'] . '-' . $m['id'],
                            $pivot_key => $model_transformed['id'],
                            $pivot_foreign_key => $m['id']
                        ]);
                    } else if ($model_transformed['_relations'][$name] == 'MorphToMany') {
                        // Many to Many Polymorphic relation -> we need to generate intermediate Models
                        $models->add([
                            '_model' => $pivot_model,
                            '_meta' => ['is_virtual' => true],
                            'id' => '_pivot-' . $model_transformed['id'] . '-' . $m['_model'] . '-' . $m['id'],
                            $m['_fk'] => $m['id'],
                            'belongsToModel_type' => $model_transformed['_model'],
                            'belongsToModel_id' => $model_transformed['id']
                        ]);
                    }
                }

            } else {
                // One to one relation -> $related_models contains a single model
                $related_model = $related_models;
                if (isset($related_model['relations']) && count($related_model['relations']) > 0) {
                    $models = $models->merge($this->getSeparateModelsFromRelations($related_model));
                }

                $models->add($related_model);
            }
        }

        return $models->map(function (array $model) {
            if (isset($model['relations']))
                unset($model['relations']);
            return $model;
        });
    }

    /**
     * @param Collection $models
     * @return JsonResponse
     */
    public function respondWithModels(Collection $models): JsonResponse
    {
        $transformed = $models->map(function (Model|ElasticsearchModel $model) {
            return $model->enrich()->transform();
        });

        if ($this->request->get('relations') == 'separate') {
            foreach ($transformed as $t) {
                if (!isset($t['relations'])) continue;
                $transformed = $transformed->merge($this->getSeparateModelsFromRelations($t));

                $transformed = $transformed->map(function (Collection|array $model) {
                    unset($model['relations']);
                    return $model;
                });
            }
        }

        $transformed = $transformed->filter();

        $transformed = $transformed->unique(function ($item) {
            return $item['_model'] . $item['id'];
        });

        return $this->respondWithData(array_values($transformed->toArray()));
    }

    /**
     * @param LengthAwarePaginator $paginator
     * @param callable|null $model_retrieval_service
     * @return JsonResponse
     */
    public function respondWithPaginatedModels(LengthAwarePaginator $paginator, callable $model_retrieval_service = null): JsonResponse
    {
        $items = new Collection($paginator->items());

        $this->meta['pagination'] = [
            'items' => $items->count(),
            'total_items' => $paginator->total(),
            'total_pages' => $paginator->lastPage(),
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage()
        ];

        if ($this->request->has('pagination')) {
            return $this->respondWithData([]);
        }

        if ($model_retrieval_service) {
            $items = $items->map($model_retrieval_service);
        }

        return $this->respondWithModels($items);
    }

    /**
     * @param array $data
     * @return JsonResponse
     */
    public function respondWithData(array $data): JsonResponse
    {
        return $this->respond([
            'meta' => $this->meta,
            'data' => $data
        ]);
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function respondNotFound(string $message = 'Model not found'): JsonResponse
    {
        return $this->respondWithError($message, HttpStatusCodeEnum::Not_Found());
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function respondUnauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return $this->respondWithError($message, HttpStatusCodeEnum::Forbidden());
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    public function respondUnauthenticated(string $message = 'Unauthenticated'): JsonResponse
    {
        return $this->respondWithError($message, HttpStatusCodeEnum::Unauthorized());
    }

    /**
     * @param array $errors
     * @param HttpStatusCodeEnum $status
     * @return JsonResponse
     */
    public function respondWithErrors(array $errors, HttpStatusCodeEnum $status): JsonResponse
    {
        return $this->respond(['errors' => $errors], $status);
    }

    /**
     * @param string $error
     * @param HttpStatusCodeEnum $status
     * @return JsonResponse
     */
    public function respondWithError(string $error, HttpStatusCodeEnum $status): JsonResponse
    {
        return $this->respondWithErrors([$error], $status);
    }

    /**
     * @param array $data
     * @param HttpStatusCodeEnum $status
     * @return JsonResponse
     */
    public function respond(array $data = [], HttpStatusCodeEnum $status = null): JsonResponse
    {
        $status = $status ?? HttpStatusCodeEnum::OK();
        return response()->json($data)->setStatusCode($status->getValue());
    }

    /**
     * @return string
     */
    protected function getModelName(): string
    {
        $model_class_name_arr = explode('\\', get_class($this));
        preg_match_all('/((?:^|[A-Z])[a-z]+)/', end($model_class_name_arr), $matches, PREG_PATTERN_ORDER);
        $class_name = implode(
            '',
            array_slice(
                $matches[0],
                0,
                count($matches[0]) - 1
            )
        );

        return '\\App\\DwhControl\\' . $this->package . '\\Models\\' . $class_name;
    }

    /**
     * Searches for controller methods with an action attribute.
     * This is used to assign non-standard controller methods a standard permission.
     * The following example would assign permissions equal to Controller->store() to the method Controller->ingest():
     *
     *  #[CustomAction(Action::STORE)]
     *  public function ingest(Request $request);
     *
     * @return array
     * @throws ReflectionException
     */
    public static function customActions(): array
    {
        $mapping = [];
        $reflection = new ReflectionClass(get_called_class());
        foreach ($reflection->getMethods() as $method) {
            if (count($attr = $method->getAttributes(CustomAction::class)) > 0) {
                $mapping[$method->getName()] = $attr[0]->newInstance()->action;
            }
        }

        return $mapping;
    }

    /**
     * @return array
     */
    protected function getRouteInfo(): array
    {
        preg_match(
            "/DwhControl\\\([A-Za-z]+)\\\Http\\\Controllers\\\([A-Za-z]+)Controller@([A-Za-z_\-]+)/",
            Route::current()->action['controller'],
            $matches
        );

        if (strpos($matches[3], '__') != false) $matches[3] = explode('__', $matches[3])[0];
        return array_slice($matches, 1, 3);
    }

    /**
     * @return string
     */
    protected function getController(): string
    {
        return explode('@', Route::current()->action['controller'])[0];
    }

    /**
     * Maps custom controller methods to the respective permissions and merges them with default permissions
     */
    protected function permissionMapping(): array
    {
        $permission_mapping = $this->defaultPermissionMapping();

        $customAction = self::getController()::customActions();
        foreach ($customAction as $method => $action) {
            $permission_mapping[$method] = $permission_mapping[$action];
        }

        return $permission_mapping;
    }

    /**
     * Returns the default mapping of controller methods to permissions.
     *
     * @return array
     */
    protected function defaultPermissionMapping(): array
    {
        return [
            'index' => 'read',
            'show' => 'read',
            'store' => 'write',
            'update' => 'write',
            'destroy' => 'write'
        ];
    }

    /**
     * @return string
     */
    public function gate(): string
    {
        list($package, $model, $action) = $this->getRouteInfo();
        return $this->permissionMapping()[$action] . '-' . $package;
    }
}
