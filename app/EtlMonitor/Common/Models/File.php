<?php

namespace App\EtlMonitor\Common\Models;

use App\EtlMonitor\Common\Traits\HasMultipleDataTypeFieldsTrait;

/**
 * App\EtlMonitor\Common\Models\File
 *
 * @property int $id
 * @property string $name
 * @property string $file_name
 * @property string $type
 * @property string $mime
 * @property int $size_bytes
 * @property string $fs_path
 * @property-read string $public_path
 * @property-read mixed $value
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\EtlMonitor\Common\Models\History[] $history
 * @property-read int|null $history_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\EtlMonitor\Common\Models\Property[] $properties
 * @property-read int|null $properties_count
 * @method static \Illuminate\Database\Eloquent\Builder|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|File query()
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereFsPath($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereMime($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereSizeBytes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|File whereType($value)
 * @mixin \Eloquent
 */
class File extends Model
{

    use HasMultipleDataTypeFieldsTrait;

    /**
     * @var array
     */
    public ?array $transformable = [
        'name', 'mime', 'type', 'public_path'
    ];

    /**
     * @param string $value
     * @return string
     */
    public function getPublicPathAttribute(string $value): string
    {
        return $this->self() . '/display';
    }

}
