<?php

namespace App\EtlMonitor\Common\Enum;

/**
 * @method static AlertEventsEnum ALERT_STARTED
 * @method static AlertEventsEnum ALERT_ENDED
 * @method static AlertEventsEnum ALERT_ESCALATED
 */
class AlertEventsEnum extends Enum
{

    private const ALERT_STARTED = 'etl_monitor.common::alert.events.started';
    private const ALERT_ENDED = 'etl_monitor.common::alert.events.ended';
    private const ALERT_ESCALATED = 'etl_monitor.common::alert.events.escalated';

}
