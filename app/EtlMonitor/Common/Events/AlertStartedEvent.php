<?php

namespace App\EtlMonitor\Common\Events;

class AlertStartedEvent extends Event implements EventInterface
{

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'AlertStartedEvent';
    }

}
