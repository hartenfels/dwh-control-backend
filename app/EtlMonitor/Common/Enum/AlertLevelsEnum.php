<?php

namespace App\EtlMonitor\Common\Enum;

/**
 * @method static AlertLevelsEnum WARNING
 * @method static AlertLevelsEnum CRITICAL
 */
class AlertLevelsEnum extends Enum
{

    private const WARNING = 'warning';
    private const CRITICAL = 'critical';

}
