<?php

return [
    'model' => \App\DwhControl\Sla\Models\SlaDefinitionLifecycle::class,
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
