<?php

return [
    'sla_statistic_history_count' => 14,
    'availability_sla_progress_history_bucket_size_min' => 30,
    'search_max_results_per_type' => 5,

    /*
     * Define a list of searchable models.
     * These models need to implement the SearchableInterface
     */
    'searchable_models' => [
        \App\EtlMonitor\Sla\Models\SlaDefinition::class,
        \App\EtlMonitor\Etl\Models\EtlDefinition::class
    ]
];
