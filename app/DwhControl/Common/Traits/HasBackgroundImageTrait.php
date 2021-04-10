<?php

namespace App\DwhControl\Common\Traits;

use App\DwhControl\Common\Models\File;
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
        $this->background_image_url = $this->background_image?->public_path ?? config('dwh_control_common.default_background_image_url');
    }

}
