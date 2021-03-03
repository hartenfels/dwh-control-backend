<?php

namespace App\EtlMonitor\Common\Http\Controllers;

use App\EtlMonitor\Api\Attributes\CustomAction;
use App\EtlMonitor\Api\Http\Controllers\Actions\Action;
use App\EtlMonitor\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultIndexMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultShowMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultStoreMethodTrait;
use App\EtlMonitor\Api\Traits\UsesDefaultUpdateMethodTrait;
use App\EtlMonitor\Common\Models\File;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FileController extends Controller
{

    use UsesDefaultIndexMethodTrait,
        UsesDefaultShowMethodTrait,
        UsesDefaultStoreMethodTrait,
        UsesDefaultUpdateMethodTrait,
        UsesDefaultDestroyMethodTrait;

    /**
     * @param File $file
     * @param string|null $name Dummy to be able to append file name to url for caching and better readability etc
     * @return BinaryFileResponse
     */
    #[CustomAction(Action::SHOW)]
    public function display(File $file, string $name = null): BinaryFileResponse
    {
        return response()->file($file->fs_path);
    }

}
