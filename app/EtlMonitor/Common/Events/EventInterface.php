<?php

namespace App\EtlMonitor\Common\Events;

use Illuminate\Broadcasting\PrivateChannel;

interface EventInterface
{

    /**
     * @return PrivateChannel
     */
    public function broadcastOn(): PrivateChannel;

    /**
     * @return string
     */
    public function broadcastAs(): string;

}
