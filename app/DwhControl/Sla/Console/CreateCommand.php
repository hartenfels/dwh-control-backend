<?php

namespace App\DwhControl\Sla\Console;

use App\DwhControl\Sla\Models\Interfaces\SlaDefinitionInterface;
use App\DwhControl\Sla\Models\SlaDefinition;
use App\DwhControl\Sla\Services\SlaCreationService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class CreateCommand extends Command
{

    /**
     * @var string
     */
    protected $signature = 'dwh-control:sla-create';

    /**
     * @var string
     */
    protected $description = 'Creates necessary SLAs';

    /**
     * @throws Exception
     */
    public function handle()
    {
        echo "Creating SLAs" . PHP_EOL;
        $t = Carbon::now();
        SlaDefinition::all()->each(function (SlaDefinitionInterface $d) use ($t) {
            SlaCreationService::make($d, $t)->invoke();
        });
    }

}
