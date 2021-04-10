<?php

namespace App\DwhControl\Common\Console;

use App\DwhControl\Etl\Models\AutomicEtlDefinition;
use App\DwhControl\Etl\Models\AutomicEtlExecution;
use App\DwhControl\Etl\Models\EtlDefinition;
use App\DwhControl\Etl\Models\Interfaces\EtlDefinitionInterface;
use App\DwhControl\Sla\Models\AvailabilitySla;
use App\DwhControl\Sla\Models\AvailabilitySlaDefinition;
use App\DwhControl\Sla\Models\DeliverableSla;
use App\DwhControl\Sla\Models\DeliverableSlaDefinition;
use App\DwhControl\Sla\Models\Interfaces\SlaDefinitionInterface;
use App\DwhControl\Sla\Models\Interfaces\SlaInterface;
use App\DwhControl\Sla\Services\SlaCreationService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Exception;
use Faker\Factory;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

class SeedCommand extends Command
{

    /**
     * @var string
     */
    protected $signature = 'dwh-control:seed {--weeks=6} {--etl_definitions=40} {--sla_definitions=10}';

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

        echo "Seeding ETL definitions" . PHP_EOL;
        $definitions_tiers = [0 => [], 1 => [], 2 => [], 3 => []];
        for ($i = 0; $i < 4; $i++) {
            for ($j = 0; $j < (int)$this->option('etl_definitions')/4; $j++) {
                $f = Factory::create();
                $n = $f->domainName . '/' . $f->domainWord . '.' . $f->iban();
                $definitions_tiers[$i][] = AutomicEtlDefinition::create([
                    'name' => $n,
                    'etl_id' => $n
                ]);
            }
        }

        echo "Seeding ETL executions" . PHP_EOL;
        foreach (CarbonPeriod::create(Carbon::today()->subWeeks((int)$this->option('weeks'))->startOfWeek(), Carbon::today()) as $day) {
            $prev_tier_executions = [];
            $executions = [];
            collect($definitions_tiers)->each(function (array $definitions, $tier) use (&$prev_tier_executions, &$executions, $day) {
                collect($definitions)->each(function (EtlDefinitionInterface $definition) use (&$prev_tier_executions, &$executions, $tier, $day) {
                    $s = (clone $day)->addHours(random_int($tier, $tier + 1));
                    $e = (clone $s)->addHours(random_int($tier + 2, $tier + 4));
                    $runtime = random_int(1, 10) > 8 ? rand(-3, 3) : null;
                    $datasets = random_int(1, 10) > 8 ? rand(-3, 3) : null;
                    $run_id = random_int(1000000, 8000000);

                    $exec = AutomicEtlExecution::create([
                        'idnr' => $run_id,
                        'etl_id' => $definition->etl_id,
                        'name' => $definition->name,
                        'alias' => $definition->name,
                        'status' => random_int(0, 10) > 8 ? 1800 : 1900,
                        'date' => [
                            'activation' => $s->format('c'),
                            'start' => $s->format('c'),
                            'end' => $e->format('c'),
                            'end_pp' => $e->format('c'),
                        ],
                        'anomaly' => [
                            'runtime' => $runtime,
                            'datasets' => $datasets
                        ]
                    ], '500-' . $run_id);

                    if ($cnt = count($prev_tier_executions) > 0) {
                        $exec->predecessor_id = $prev_tier_executions[random_int(0, $cnt - 1)]->getId();
                        $exec->save();
                    }
                    $executions[] = $exec;
                });

                $prev_tier_executions = $executions;
            });
        }

        echo "Calculating ETL statistics" . PHP_EOL;
        AutomicEtlDefinition::all()->each(function (AutomicEtlDefinition $definition) {
            $definition->calculateStatistic();
        });

        /** @var Collection<EtlDefinitionInterface> $etls */
        $etls = EtlDefinition::all();

        echo "Seeding SLA definitions" . PHP_EOL;
        for ($i = 0; $i < (int)$this->option('sla_definitions') / 2; $i++) {
            $f = Factory::create();
            $rules = [
                'etls' => []
            ];
            for ($j = 0; $j < random_int(1, 4); $j++) {
                $etl = $etls->get(random_int(0, $etls->count() - 1));
                $rules['etls'][] = [
                    'id' => $etl->getId(),
                    'etl_id' => $etl->etl_id
                ];
            }
            DeliverableSlaDefinition::create([
                'name' => $f->name,
                'lifecycle_id' => random_int(1, 3),
                'source' => 'etl',
                'rules' => $rules
            ]);

            $f = Factory::create();
            AvailabilitySlaDefinition::create([
                'name' => $f->name,
                'lifecycle_id' => random_int(1, 3),
                'target_percent' => random_int(80, 100)
            ]);
        }

        echo "Seeding timeranges" . PHP_EOL;
        DeliverableSlaDefinition::all()->merge(AvailabilitySlaDefinition::all())->each(function (SlaDefinitionInterface $d) {
            if (random_int(1, 6) > 4) {
                $f = Factory::create();
                $days = random_int(3, 6);
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
        AvailabilitySlaDefinition::all()->each(function (SlaDefinitionInterface $d) {
            foreach (CarbonPeriod::create(Carbon::today()->subWeeks((int)$this->option('weeks'))->startOfWeek(), Carbon::today()) as $day) {
                SlaCreationService::make($d, $day)->invoke()->each(function (SlaInterface $sla) use ($day) {
                    /** @var Carbon $cursor */
                    $cursor = $sla->range_start;
                    do {
                        $sla->addProgress($cursor, progress_percent: random_int(60, 100), source: 'Seed', calculate: true);
                    } while($cursor->addMinutes(30)->lt($sla->range_end));
                });
            }
        });

        DeliverableSlaDefinition::all()->each(function (SlaDefinitionInterface $d) {
            foreach (CarbonPeriod::create(Carbon::today()->subWeeks((int)$this->option('weeks'))->startOfWeek(), Carbon::today()) as $day) {
                SlaCreationService::make($d, $day)->invoke();
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
