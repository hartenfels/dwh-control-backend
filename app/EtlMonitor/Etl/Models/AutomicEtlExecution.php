<?php

namespace App\EtlMonitor\Etl\Models;

use App\EtlMonitor\Common\Models\ElasticsearchModel;
use App\EtlMonitor\Etl\Transfer\Anomaly;
use App\EtlMonitor\Etl\Models\Interfaces\EtlExecutionInterface;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\Pure;

class AutomicEtlExecution extends ElasticsearchModel implements EtlExecutionInterface
{

    /**
     * @var string
     */
    protected $connectionName = 'etl_executions_automic';

    /**
     * @var string[]
     */
    protected $fillable = [
        '_id', 'name', 'alias', 'etl_id', 'status', 'date', 'anomaly'
    ];

    protected ?array $transformable = [
        '_id', 'name', 'alias', 'etl_id',
        'date_activation', 'date_start', 'date_end', 'date_end_pp',
        'anomaly_long_running', 'anomaly_short_running', 'anomaly_factor'
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        '@timestamp' => 'timestamp',
        'date_activation' => 'timestamp',
        'date_start' => 'timestamp',
        'date_end' => 'timestamp',
        'date_end_pp' => 'timestamp'
    ];

    /**
     * @return CarbonInterface|null
     */
    public function getDateActivationAttribute (): ?CarbonInterface
    {
        if (!is_array($this->date)) return null;
        return Carbon::parse($this->date['activation']);
    }

    /**
     * @return CarbonInterface|null
     */
    public function getDateStartAttribute (): ?CarbonInterface
    {
        if (!is_array($this->date)) return null;
        return Carbon::parse($this->date['start']);
    }

    /**
     * @return CarbonInterface|null
     */
    public function getDateEndAttribute (): ?CarbonInterface
    {
        if (!is_array($this->date)) return null;
        return Carbon::parse($this->date['end']);
    }

    /**
     * @return CarbonInterface|null
     */
    public function getDateEndPpAttribute (): ?CarbonInterface
    {
        if (!is_array($this->date)) return null;
        return Carbon::parse($this->date['end_pp']);
    }

    /**
     * @return bool
     */
    public function getAnomalyLongRunningAttribute (): bool
    {
        return isset($this->anomaly['long_running']) && $this->anomaly['long_running'] == 'true';
    }

    /**
     * @return bool
     */
    public function getAnomalyShortRunningAttribute (): bool
    {
        return isset($this->anomaly['short_running']) && $this->anomaly['short_running'] == 'true';
    }

    /**
     * @return ?float
     */
    public function getAnomalyFactorAttribute (): ?float
    {
        return isset($this->anomaly['factor']) ? $this->anomaly['factor'] : null;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return 'mdi-atom';
    }

    /**
     * @return CarbonInterface
     */
    public function getStart(): CarbonInterface
    {
        return $this->date_start;
    }

    /**
     * @return CarbonInterface
     */
    public function getEnd(): CarbonInterface
    {
        return $this->date_end;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        if ($this->status >= 1900 && $this->status <= 1999) {
            return 'ok';
        }
        if ($this->status >= 1800 && $this->status <= 1899) {
            return 'error';
        }
        return 'other';
    }

    /**
     * @return Collection<Anomaly>
     */
    public function getAnomaly(): Collection
    {
        if (is_null($this->anomalies)) return new Collection();

        $anomalies = new Collection();
        collect(array_keys($this->anomalies))->each(function (string $anomaly_type) use (&$anomalies) {
            $anomalies->push(new Anomaly($anomaly_type, $this->anomalies[$anomaly_type]));
        });

        return $anomalies;
    }
}
