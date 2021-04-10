<?php

namespace App\DwhControl\Common\Enum;

/**
 * @method static BroadcastQueueEnum QUEUE_DEFAULT
 * @method static BroadcastQueueEnum QUEUE_AUTOMATION
 * @method static BroadcastQueueEnum QUEUE_CORE
 * @method static BroadcastQueueEnum QUEUE_MONITORING
 */
class BroadcastQueueEnum extends Enum
{

    private const QUEUE_DEFAULT = 'queue-default';
    private const QUEUE_AUTOMATION = 'queue-automation';
    private const QUEUE_CORE = 'queue-core';
    private const QUEUE_MONITORING = 'queue-monitoring';

}
