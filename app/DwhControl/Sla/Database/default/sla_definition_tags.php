<?php

return [
    'model' => \App\DwhControl\Sla\Models\SlaDefinitionTag::class,
    'items' => [
        [
            'name' => 'experimental',
            'color' => 'secondary',
            'icon' => 'mdi-flask-outline',
            'hide_name' => true
        ],
        [
            'name' => 'productive',
            'color' => 'primary',
            'icon' => 'mdi-web',
            'hide_name' => true
        ],
        [
            'name' => 'inactive',
            'icon' => 'mdi-window-close',
            'hide_name' => true
        ]
    ]
];
