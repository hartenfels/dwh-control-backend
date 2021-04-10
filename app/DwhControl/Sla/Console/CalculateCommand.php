<?php

namespace App\DwhControl\Sla\Console;

use App\DwhControl\Sla\Models\Interfaces\SlaDefinitionInterface;
use App\DwhControl\Sla\Models\Interfaces\SlaInterface;
use App\DwhControl\Sla\Models\Sla;
use App\DwhControl\Sla\Models\SlaDefinition;
use App\DwhControl\Sla\Services\SlaCreationService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class CalculateCommand extends Command
{

    /**
     * @var string
     */
    protected $signature = 'dwh-control:sla-calculate';

    /**
     * @var string
     */
    protected $description = 'Calculates current SLAs';

    /**
     * @throws Exception
     */
    public function handle()
    {
        echo "Calculating SLAs" . PHP_EOL;
        Sla::where('is_open')->get()->each(function (SlaInterface $sla) {
            echo $sla->definition->name . PHP_EOL;
            $sla->calculate();
        });
    }

}
