<?php

namespace App\EtlMonitor\Common\Listeners;

use App\EtlMonitor\Common\Events\Event;

interface ListenerInterface
{

    public function handle(Event $event): void;

}
