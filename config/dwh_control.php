<?php

return [
    'sla_statistic_history_count' => 14,
    'sla_calculate_affecting_etls' => [
        'depth' => 3
    ],
    'availability_sla_progress_history_bucket_size_min' => 30,


    'etl_executions_elasticsearch_maxtake' => 1000,
    'etl_execution_mapping' => [
        'automic' => [
            'fields' => [ // definition => execution
                'etl_id' => 'etl_id',
                'name' => 'alias'
            ],
            'depends_on' => [
                'depends_on_field' => 'predecessor_idnr',
                'references_field' => 'idnr'
            ]
        ]
    ],



    /*
     * Define a list of searchable models.
     * These models need to implement the SearchableInterface
     */
    'searchable_models' => [
        \App\DwhControl\Sla\Models\SlaDefinition::class,
        \App\DwhControl\Etl\Models\EtlDefinition::class
    ],
    'search_max_results_per_type' => 5
];
