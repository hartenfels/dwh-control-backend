<?php

namespace App\DwhControl\Common\Listeners;

use App\DwhControl\Common\Events\Event;

interface ListenerInterface
{

    public function handle(Event $event): void;

}
