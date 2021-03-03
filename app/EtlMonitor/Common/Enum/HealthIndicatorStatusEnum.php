<?php

namespace App\EtlMonitor\Common\Enum;

/**
 * @method static HealthIndicatorStatusEnum HEALTH_OK
 * @method static HealthIndicatorStatusEnum HEALTH_INFO
 * @method static HealthIndicatorStatusEnum HEALTH_WARNING
 * @method static HealthIndicatorStatusEnum HEALTH_CRITICAL
 */
class HealthIndicatorStatusEnum extends Enum
{

    private const HEALTH_OK = 'ok';
    private const HEALTH_INFO = 'info';
    private const HEALTH_WARNING = 'warning';
    private const HEALTH_CRITICAL = 'critical';

    /**
     * @return array
     */
    public static function orderedByCriticality(): array
    {
        return [
            static::HEALTH_OK(),
            static::HEALTH_INFO(),
            static::HEALTH_WARNING(),
            static::HEALTH_CRITICAL()
        ];
    }

}
