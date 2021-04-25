<?php

namespace App\DwhControl\Sla\Console;

use App\DwhControl\Sla\Models\DeliverableSlaDefinition;
use Exception;
use Illuminate\Console\Command;

class CalculateAffectingEtlsCommand extends Command
{

    /**
     * @var string
     */
    protected $signature = 'dwh-control:sla-calculate-affecting-etls';

    /**
     * @var string
     */
    protected $description = 'Calculates SLA definitions\' affecting ETLs';

    /**
     * @throws Exception
     */
    public function handle()
    {
        echo "Calculating affecting ETLs" . PHP_EOL;
        DeliverableSlaDefinition::get()->each(function (DeliverableSlaDefinition $d) {
            $d->calculateAffectingEtls();
        });
    }

}
