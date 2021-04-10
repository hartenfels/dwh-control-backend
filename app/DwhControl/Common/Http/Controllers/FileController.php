<?php

namespace App\DwhControl\Common\Http\Controllers;

use App\DwhControl\Api\Attributes\CustomAction;
use App\DwhControl\Api\Http\Controllers\Actions\Action;
use App\DwhControl\Api\Traits\UsesDefaultDestroyMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultIndexMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultShowMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultStoreMethodTrait;
use App\DwhControl\Api\Traits\UsesDefaultUpdateMethodTrait;
use App\DwhControl\Common\Models\File;
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
