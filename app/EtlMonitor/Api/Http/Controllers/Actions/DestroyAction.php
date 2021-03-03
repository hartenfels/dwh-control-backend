<?php

namespace App\EtlMonitor\Api\Http\Controllers\Actions;

use App\EtlMonitor\Api\Http\Requests\Request;
use App\EtlMonitor\Common\Exceptions\ModelNotFoundException;
use App\EtlMonitor\Common\ModelFinderService;
use Exception;

class DestroyAction extends Action
{

    /**
     * @var int
     */
    protected int $id;

    /**
     * @param Request $request
     * @param string $model_name
     * @param int $id
     * @return static
     */
    public static function prepare(Request $request, string $model_name, int $id): self
    {
        $action = new self($request, $model_name);
        $action->setModel($id);

        return $action;
    }

    /**
     * @return void
     * @throws Exception
     */
    public function invoke(): void
    {
        $this->destroyModel();
    }

    /**
     * @throws Exception
     */
    public function __invoke(): void
    {
        $this->invoke();
    }

    /**
     * @return $this
     * @throws Exception
     */
    protected function destroyModel(): self
    {
        $this->model->delete();

        return $this;
    }

    /**
     * @param int $id
     * @return $this
     * @throws ModelNotFoundException
     */
    public function setModel(int $id): self
    {
        $this->id = $id;
        $this->model = ModelFinderService::findOrFail($this->model_name, $this->id);

        return $this;
    }

}
