<?php

return [
    'sla_statistic_history_count' => 14,
    'availability_sla_progress_history_bucket_size_min' => 30,
    'search_max_results_per_type' => 5,
    'etl_executions_elasticsearch_maxtake' => 1000,

    /*
     * Define a list of searchable models.
     * These models need to implement the SearchableInterface
     */
    'searchable_models' => [
        \App\DwhControl\Sla\Models\SlaDefinition::class,
        \App\DwhControl\Etl\Models\EtlDefinition::class
    ]
];
