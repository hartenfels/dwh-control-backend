<?php

namespace App\DwhControl\Common\Models;

use App\DwhControl\Api\Http\Transformers\GenericTransformer;
use App\DwhControl\Common\Enum\DatabaseDataTypesEnum;
use App\DwhControl\Common\Enum\PropertyTypesEnum;
use App\DwhControl\Common\Events\FrontendModelUpdateRequestEvent;
use App\DwhControl\Common\Exceptions\InvalidPropertyDataTypeException;
use App\DwhControl\Common\Models\Interfaces\ModelInterface;
use App\DwhControl\Common\Traits\HasHistoryTrait;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Log;
use ReflectionClass;
use ReflectionException;

abstract class Model extends \Illuminate\Database\Eloquent\Model implements ModelInterface
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
     * @var string
     */
    protected string $prefix;

    /**
     * @var array
     */
    protected array $transform_ignore = ['icon'];

    /**
     * Model constructor.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->table = 'dwh_control_' . strtolower($this::package()) . '__' . $this->getTable();
        parent::__construct($attributes);
    }

    /**
     * @return bool|null
     * @throws ReflectionException
     * @throws Exception
     */
    public function delete()
    {
        $this->properties()->delete();

        return parent::delete();
    }

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
        $split = explode('__', $this->getTable());

        return end($split);
    }

    /**
     * @return string
     */
    public function fk(): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $this::model())) . '_id';
    }

    /**
     * @return string
     */
    public function self(): string
    {
        return url('api/v1/' . strtolower($this::package()) . '/' . $this->entity() . '/' . $this->id);
    }

    /**
     * @return Model
     */
    public function enrich(): Model
    {
        return $this;
    }

    /**
     * @return array
     */
    public function transform(): array
    {
        $transformerClass = "App\\DwhControl\\" . static::package() . "\\Http\\Transformers\\" . static::model() . "Transformer";
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
     * @param string $message
     * @param string|null $trace
     * @return string
     */
    public function logDebug(string $message, ?string $trace = null): string
    {
        if (!config('app.debug')) return '';

        $trace = $trace ?? uniqid();
        Log::debug($this->getLogString($message, $trace));

        return $trace;
    }

    /**
     * @param string $message
     * @param string|null $trace
     * @return string
     */
    public function logNotice(string $message, ?string $trace = null): string
    {
        $trace = $trace ?? $trace = uniqid();
        Log::notice($this->getLogString($message, $trace));

        return $trace;
    }

    /**
     * @param string $message
     * @param string|null $trace
     * @return string
     */
    public function logInfo(string $message, ?string $trace = null): string
    {
        $trace = $trace ?? $trace = uniqid();
        Log::info($this->getLogString($message, $trace));

        return $trace;
    }

    /**
     * @param string $message
     * @param string|null $trace
     * @return string
     */
    public function logWarning(string $message, ?string $trace = null): string
    {
        $trace = $trace ?? $trace = uniqid();
        Log::warning($this->getLogString($message, $trace));

        return $trace;
    }

    /**
     * @param string $message
     * @param string|null $trace
     * @return string
     */
    public function logCritical(string $message, ?string $trace = null): string
    {
        $trace = $trace ?? $trace = uniqid();
        Log::critical($this->getLogString($message, $trace));

        return $trace;
    }

    /**
     * @param string $message
     * @param string $trace
     * @return string
     */
    private function getLogString(string $message, string $trace): string
    {
        return sprintf('[%s] %s - %s', $trace, getCallingFunctionName($this->id), $message);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon ?? 'mdi-help-circle-outline';
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public static function getRelationNames(): array
    {
        $reflection = new ReflectionClass(static::class);
        $relations = [];
        foreach ($reflection->getMethods() as $method) {
            $return_type = $method->getReturnType();
            if (is_null($return_type)) {
                continue;
            }

            $namespace_parts = explode('\\', $return_type->getName());
            $class_name = $namespace_parts[count($namespace_parts) - 1];
            unset($namespace_parts[count($namespace_parts) - 1]);
            $namespace = implode('\\', $namespace_parts);

            if ($namespace == 'Illuminate\\Database\\Eloquent\\Relations') {
                $relations[$method->getName()] = $class_name;
            }
        };

        return $relations;
    }

    /**
     * @return MorphMany
     */
    public function properties(): MorphMany
    {
        return $this->morphMany(Property::class, 'belongsToModel');
    }

    /**
     * @param PropertyTypesEnum $type
     * @return Collection
     */
    public function getProperties(PropertyTypesEnum $type): Collection
    {
        return $this->properties()->where('type', $type)->get();
    }

    /**
     * @param PropertyTypesEnum $type
     * @param string $name
     * @param bool $create
     * @param DatabaseDataTypesEnum $create_datatype
     * @return Property
     */
    public function getProperty(PropertyTypesEnum $type, string $name, $create = false, DatabaseDataTypesEnum $create_datatype = null): ?Property
    {
        /** @var Property $property */
        $property = $this->properties()->where('type', $type)->where('name', $name)->first();
        if (is_null($property) && $create) {
            $create_datatype = $create_datatype ?? DatabaseDataTypesEnum::DATATYPE_STRING();
            $property = $this->properties()->create([
                'type' => $type,
                'name' => $name,
                'datatype' => $create_datatype
            ]);
        }

        return $property;
    }

    /**
     * @param PropertyTypesEnum $type
     * @param string $name
     * @param mixed $default
     * @return mixed|null
     */
    public function getPropertyValue(PropertyTypesEnum $type, string $name, $default = null): mixed
    {
        $property = $this->getProperty($type, $name);
        if (is_null($property)) return $default;

        return $property->getValue();
    }

    /**
     * @param PropertyTypesEnum $type
     * @param string $name
     * @param mixed $value
     * @param DatabaseDataTypesEnum $datatype
     * @return Property
     * @throws InvalidPropertyDataTypeException
     */
    public function setProperty(PropertyTypesEnum $type, string $name, $value, DatabaseDataTypesEnum $datatype = null): Property
    {
        $datatype = $datatype ?? $this->getPropertyValueTypeByValue($value);
        $value_field = 'value_' . $datatype;

        $property = $this->getProperty($type, $name, true, $datatype)->fill([
            $value_field => $value,
            'datatype' => $datatype,
            'is_active' => true
        ]);

        $property->save();

        return $property;
    }

    /**
     * @param PropertyTypesEnum $type
     * @throws Exception
     */
    public function deleteProperties(PropertyTypesEnum $type): self
    {
        $this->getProperties($type)->each(function (Property $property) {
            $property->delete();
        });

        return $this;
    }

    /**
     * @param PropertyTypesEnum $type
     * @param string $name
     * @throws Exception
     */
    public function deleteProperty(PropertyTypesEnum $type, string $name): self
    {
        if (!is_null($p = $this->getProperty($type, $name))) {
            $p->delete();
        }

        return $this;
    }

    /**
     * @param mixed $value
     * @return string
     * @throws InvalidPropertyDataTypeException
     */
    public function getPropertyValueTypeByValue($value): string
    {
        $mapping = [
            'boolean' => ['boolean'],
            'float' => ['float', 'double'],
            'bigint' => ['integer'],
            'string' => ['string']
        ];

        foreach ($mapping as $field => $types) {
            if (in_array(gettype($value), $types)) {
                return $field;
            }
        }

        throw new InvalidPropertyDataTypeException(gettype($value));
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
