<?php

return [
    'model' => \App\EtlMonitor\Sla\Models\SlaDefinitionStatus::class,
    'items' => [
        [
            'name' => 'draft'
        ],
        [
            'name' => 'active'
        ],
        [
            'name' => 'retired'
        ]
    ]
];
