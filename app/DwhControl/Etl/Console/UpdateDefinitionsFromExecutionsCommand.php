<?php

namespace App\DwhControl\Etl\Console;

use App\DwhControl\Etl\Models\EtlDefinition;
use Exception;
use Illuminate\Console\Command;

class UpdateDefinitionsFromExecutionsCommand extends Command
{

    /**
     * @var string
     */
    protected $signature = 'dwh-control:etl_update-definitions-from-execution';

    /**
     * @var string
     */
    protected $description = 'Updates ETL definitions by loading data from their last execution';


    /**
     * @throws Exception
     */
    public function handle()
    {
        echo "Updating ETL definitions" . PHP_EOL;
        EtlDefinition::get()->each(function (EtlDefinition $d) {
            $d->updateFromExecution();
        });
    }

}
