<?php

namespace App\DwhControl\Common\Enum;

/**
 * @method static AlertEventsEnum ALERT_STARTED
 * @method static AlertEventsEnum ALERT_ENDED
 * @method static AlertEventsEnum ALERT_ESCALATED
 */
class AlertEventsEnum extends Enum
{

    private const ALERT_STARTED = 'dwh_control.common::alert.events.started';
    private const ALERT_ENDED = 'dwh_control.common::alert.events.ended';
    private const ALERT_ESCALATED = 'dwh_control.common::alert.events.escalated';

}
