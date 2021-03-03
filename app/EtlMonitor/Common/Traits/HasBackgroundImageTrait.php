<?php

namespace App\EtlMonitor\Common\Traits;

use App\EtlMonitor\Common\Models\File;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasBackgroundImageTrait
{

    /**
     * @return HasOne
     */
    public function background_image(): HasOne
    {
        return $this->hasOne(File::class,  'id', 'background_image_file_id');
    }

    /**
     *
     */
    protected function enrichBackgroundImage(): void
    {
        $this->background_image_url = $this->background_image?->public_path ?? config('etl_monitor_common.default_background_image_url');
    }

}
