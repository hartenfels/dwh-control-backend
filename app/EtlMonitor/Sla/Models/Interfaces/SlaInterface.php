<?php

namespace App\EtlMonitor\Sla\Models\Interfaces;

use App\EtlMonitor\Common\Models\Interfaces\ModelInterface;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

interface SlaInterface extends ModelInterface
{

    /**
     * @return SlaInterface|null
     */
    public function next(): ?SlaInterface;

    /**
     * @return HasMany
     */
    public function progress(): HasMany;

    /**
     * @return belongsTo
     */
    public function progress_last_intime(): belongsTo;

    /**
     * @return belongsTo
     */
    public function progress_first_intime_achieved(): belongsTo;

    /**
     * @return belongsTo
     */
    public function progress_last_late(): belongsTo;

    /**
     * @return belongsTo
     */
    public function progress_first_late_achieved(): belongsTo;

    /**
     * @return HasMany
     */
    public function achievement_conditions(): HasMany;

    /**
     * @return $this
     */
    public function fetchProgress(): self;

    /**
     * @param CarbonInterface $time
     * @param float $progress_percent
     * @param string $source
     * @param bool $calculate
     * @return SlaProgressInterface
     */
    public function addProgress(CarbonInterface $time, float $progress_percent, string $source, bool $calculate = false): SlaProgressInterface;

    /**
     * @param CarbonInterface|null $time
     * @param bool $calculate
     * @param bool $fetch
     * @return $this
     */
    public function updateProgress(CarbonInterface $time = null, bool $calculate = true, bool $fetch = true): self;

    /**
     * @param CarbonInterface|null $time
     * @return SlaInterface
     */
    public function calculate(CarbonInterface $time = null): self;

    /**
     * @return $this
     */
    public function calculateStatistics(): self;

    /**
     * @param TimerangeInterface $timerange
     * @return bool
     */
    public function matchesTimerange(TimerangeInterface $timerange): bool;

    /**
     * @param SlaProgressInterface|null $progress
     * @param SlaProgressInterface|null $progress_first_achieved
     * @return $this
     */
    function setProgressIntime(SlaProgressInterface $progress = null, SlaProgressInterface $progress_first_achieved = null): self;

    /**
     * @param SlaProgressInterface|null $progress
     * @param SlaProgressInterface|null $progress_first_achieved
     * @return $this
     */
    function setProgressLate(SlaProgressInterface $progress = null, SlaProgressInterface $progress_first_achieved = null): self;

    /**
     * @return $this
     */
    function setClosed(): SlaInterface;

    /**
     * @param SlaProgressInterface $progress
     * @return $this
     */
    function setAchieved(SlaProgressInterface $progress): self;

    /**
     * @param SlaProgressInterface|null $progress_last_intime
     * @param SlaProgressInterface|null $progress_last_late
     * @return $this
     */
    function setFailed(SlaProgressInterface $progress_last_intime = null, SlaProgressInterface $progress_last_late = null): self;

    /**
     * @return $this
     */
    function setWaiting(): self;

}
