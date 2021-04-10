<?php

namespace App\DwhControl\Common\Enum;

/**
 * @method static PropertyTypesEnum MONITOR_SETTING Contains settings for monitor time frames and bucket size
 * @method static PropertyTypesEnum MONITORING_MONITOR_VALUE Average current value for a single metric
 * @method static PropertyTypesEnum MONITORING_MONITOR_HISTORY_VALUE Historic values for a single metric in buckets
 */
class PropertyTypesEnum extends Enum
{

    private const MONITOR_SETTING = 'MonitorSetting';
    private const MONITORING_MONITOR_VALUE = 'MonitoringMonitorValue';
    private const MONITORING_MONITOR_HISTORY_VALUE = 'MonitoringMonitorHistoryValue';

}
