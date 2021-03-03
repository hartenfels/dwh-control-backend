<?php

namespace App\EtlMonitor\Common\Events;

use App\EtlMonitor\Common\Models\Model;

class FrontendModelUpdateRequestEvent extends Event implements EventInterface
{

    /**
     * @var string
     */
    public string $model_name;

    /**
     * @var int
     */
    public int $id;

    /**
     * @var string
     */
    public string $channel = 'frontend-update-requests';

    /**
     * FrontendModelUpdateRequestEvent constructor.
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model_name = $model::model();
        $this->id = $model->id;
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'Common.FrontendModelUpdateRequest';
    }
}
