<?php

namespace App\EtlMonitor\Common\Models;

use App\EtlMonitor\Api\Http\Transformers\GenericTransformer;
use App\EtlMonitor\Common\Events\FrontendModelUpdateRequestEvent;
use App\EtlMonitor\Common\Models\Interfaces\ModelInterface;
use App\EtlMonitor\Common\Traits\HasHistoryTrait;

abstract class ElasticsearchModel extends \Matchory\Elasticsearch\Model implements ModelInterface
{

    use HasHistoryTrait;

    /**
     * @var array
     */
    protected $fillable = [];

    /**
     * @var array|null
     */
    protected ?array $transformable = null;

    /**
     * @var array
     */
    protected array $transform_ignore = ['icon'];

    /**
     * @var string
     */
    protected string $entity = '';

    /**
     * @return mixed
     */
    public static function model(): string
    {
        $class = explode('\\', static::class);
        return end($class);
    }

    /**
     * @return string
     */
    public static function package(): string
    {
        $class = explode('\\', static::class);
        return $class[count($class) - 3];
    }

    /**
     * @return string
     */
    public function entity(): string
    {
        return $this->entity;
    }

    /**
     * @return string
     */
    public function self(): string
    {
        return url('api/v1/' . strtolower($this::package()) . '/' . $this->entity() . '/' . $this->id);
    }

    /**
     * @return ElasticsearchModel
     */
    public function enrich(): self
    {
        return $this;
    }

    /**
     * @return string
     */
    public function fk(): string
    {
        return '';
    }

    /**
     * @return array
     */
    public static function getRelationNames(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function transform(): array
    {
        $transformerClass = "App\\EtlMonitor\\" . static::package() . "\\Http\\Transformers\\" . static::model() . "Transformer";
        $transformer = class_exists($transformerClass) ? new $transformerClass : new GenericTransformer();

        return $transformer($this);
    }

    /**
     * @return array
     */
    public function getTransformable(): array
    {
        $transformable = $this->transformable ?? $this->fillable;
        $ignore = $this->transform_ignore;
        return array_filter($transformable, function ($t) use ($ignore) {
            return !in_array($t, $ignore);
        });
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon ?? 'mdi-help-circle-outline';
    }

    /**
     * @return $this
     */
    public function requestFrontendRefresh(): self
    {
        FrontendModelUpdateRequestEvent::dispatch($this);

        return $this;
    }

}
