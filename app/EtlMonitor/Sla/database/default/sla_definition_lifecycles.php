<?php

return [
    'model' => \App\EtlMonitor\Sla\Models\SlaDefinitionLifecycle::class,
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
