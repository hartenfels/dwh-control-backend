<?php

namespace App\EtlMonitor\Common\Console;

use App\EtlMonitor\Sla\Models\DeliverableSlaDefinition;
use App\EtlMonitor\Sla\Models\Interfaces\SlaInterface;
use App\EtlMonitor\Sla\Services\SlaCreationService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Faker\Factory;
use Illuminate\Console\Command;

class SeedCommand extends Command
{

    /**
     * @var string
     */
    protected $signature = 'etl_monitor:seed {--definitions=10}';

    /**
     * @var string
     */
    protected $description = 'Seed test data';

    /**
     * @throws Exception
     */
    public function handle()
    {
        echo "Seeding demo data" . PHP_EOL;

        echo "Seeding SLA definitions" . PHP_EOL;
        for ($i = 0; $i < (int)$this->option('definitions'); $i++) {
            $f = Factory::create();
            DeliverableSlaDefinition::create([
                'name' => $f->name
            ]);
        }

        echo "Seeding timeranges" . PHP_EOL;
        DeliverableSlaDefinition::all()->each(function (DeliverableSlaDefinition $d) {
            for ($i = 1; $i <= 7; $i++) {
                $f = Factory::create();
                $d->daily_timeranges()->create([
                    'anchor' => $i,
                    'range_start' => '00:00',
                    'range_end' => $f->time()
                ]);
            }
        });

        echo "Creating SLAs" . PHP_EOL;
        DeliverableSlaDefinition::all()->each(function (DeliverableSlaDefinition $d) {
            foreach (CarbonPeriod::create(Carbon::today()->startOfWeek(), Carbon::today()->endOfWeek()) as $day) {
                SlaCreationService::make($d, $day)->invoke()->each(function (SlaInterface $sla) use ($day) {
                    $f = Factory::create();
                    $pd = clone $day;
                    $pd->setTimeFromTimeString($f->time());
                    $sla->addProgress($pd, progress_percent: 100, source: 'Seed', calculate: true);
                });
            }
        });

        echo "Seeding done" . PHP_EOL;
    }

}
