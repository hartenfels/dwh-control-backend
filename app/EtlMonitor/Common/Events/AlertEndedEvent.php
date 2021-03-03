<?php

namespace App\EtlMonitor\Common\Events;

class AlertEndedEvent extends Event implements EventInterface
{

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'AlertEndedEvent';
    }

}
