<?php

namespace App\DwhControl\Common\Events;

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
