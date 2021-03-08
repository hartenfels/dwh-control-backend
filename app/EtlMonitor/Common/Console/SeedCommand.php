<?php

namespace App\EtlMonitor\Common\Console;

use App\EtlMonitor\Sla\Models\AvailabilitySla;
use App\EtlMonitor\Sla\Models\AvailabilitySlaDefinition;
use App\EtlMonitor\Sla\Models\DeliverableSla;
use App\EtlMonitor\Sla\Models\DeliverableSlaDefinition;
use App\EtlMonitor\Sla\Models\Interfaces\SlaDefinitionInterface;
use App\EtlMonitor\Sla\Models\Interfaces\SlaInterface;
use App\EtlMonitor\Sla\Models\Interfaces\SlaProgressInterface;
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
        for ($i = 0; $i < (int)$this->option('definitions') / 2; $i++) {
            $f = Factory::create();
            DeliverableSlaDefinition::create([
                'name' => $f->name,
                'status_id' => random_int(1, 3)
            ]);

            $f = Factory::create();
            AvailabilitySlaDefinition::create([
                'name' => $f->name,
                'status_id' => random_int(1, 3),
                'target_percent' => random_int(80, 100)
            ]);
        }

        echo "Seeding timeranges" . PHP_EOL;
        DeliverableSlaDefinition::all()->merge(AvailabilitySlaDefinition::all())->each(function (SlaDefinitionInterface $d) {
            if (random_int(1, 6) > 4) {
                $f = Factory::create();
                $days = random_int(3, 7);
                $d->weekly_timeranges()->create([
                    'range_start' => '00:00',
                    'range_end' => ((int)$f->time('H') + $days*24) . ':' . $f->time('i') ,
                    'error_margin_minutes' => random_int(30, 120)
                ]);
            } else {
                for ($i = 1; $i <= 7; $i++) {
                    $f = Factory::create();
                    $d->daily_timeranges()->create([
                        'anchor' => $i,
                        'range_start' => '00:00',
                        'range_end' => $f->time('H:i'),
                        'error_margin_minutes' => random_int(30, 120)
                    ]);
                }
            }
        });

        echo "Creating SLAs" . PHP_EOL;
        DeliverableSlaDefinition::all()->merge(AvailabilitySlaDefinition::all())->each(function (SlaDefinitionInterface $d) {
            foreach (CarbonPeriod::create(Carbon::today()->subWeeks(6)->startOfWeek(), Carbon::today()) as $day) {
                SlaCreationService::make($d, $day)->invoke()->each(function (SlaInterface $sla) use ($day) {
                    $pd = clone $sla->range_start;
                    $mins_end = $sla->range_start->diffInMinutes($sla->range_end) * 1.3;
                    $random_min = random_int(0, $mins_end);
                    $pd->addMinutes($random_min);
                    if ($sla instanceof AvailabilitySla) {
                        /** @var Carbon $cursor */
                        $cursor = $sla->range_start;
                        do {
                            $sla->addProgress($cursor, progress_percent: random_int(60, 100), source: 'Seed', calculate: true);
                        } while($cursor->addMinutes(30)->lt($sla->range_end));
                    }
                    if ($sla instanceof DeliverableSla) {
                        $sla->addProgress($pd, progress_percent: 100, source: 'Seed', calculate: true);
                    }
                });
            }
        });

        echo "Calculating SLAs" . PHP_EOL;
        DeliverableSla::get()->each(function (DeliverableSla $sla) {
            $sla->updateProgress();
            $sla->fresh();
            $sla->calculateStatistics();
        });
        AvailabilitySla::get()->each(function (AvailabilitySla $sla) {
            $sla->updateProgress();
            $sla->fresh();
            $sla->calculateStatistics();
        });

        DeliverableSlaDefinition::get()->merge(AvailabilitySlaDefinition::all())->each(function (SlaDefinitionInterface $sla) {
            $sla->calculateStatistics();
        });

        echo "Seeding done" . PHP_EOL;
    }

}
