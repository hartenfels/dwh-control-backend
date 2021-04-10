<?php

namespace App\DwhControl\Common\Subscribers;

use Illuminate\Events\Dispatcher;

interface SubscriberInterface
{

    public function subscribe(Dispatcher $events): void;

}
