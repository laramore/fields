<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default proxies
    |--------------------------------------------------------------------------
    |
    | These options define all proxy configurations.
    |
    */

    'class' => \Laramore\Proxies\FieldProxy::class,

    'configurations' => [
        'dry' => [
            'static' => true,
        ],
        'cast' => [
            'static' => true,
        ],
        'transform' => [
            'static' => true,
        ],
        'serialize' => [
            'static' => true,
        ],
        'getDefault' => [
            'static' => true,
        ],
        'get' => [
            'templates' => [
                'name' => '${methodname}^{identifier}Attribute',
                'multi_name' => '${methodname}Attribute',
            ],
        ],
        'set' => [
            'templates' => [
                'name' => '${methodname}^{identifier}Attribute',
                'multi_name' => '${methodname}Attribute',
            ],
        ],
        'reset' => [
            'templates' => [
                'name' => '${methodname}^{identifier}Attribute',
                'multi_name' => '${methodname}Attribute',
            ],
        ],
        'relate' => [
            'templates' => [
                'name' => '${identifier}',
            ],
        ],
        'where' => [
            'requirements' => ['instance'],
            'templates' => [
                'name' => 'scope^{methodname}^{identifier}',
                'multi_name' => 'scope^{methodname}',
            ],
        ],
        'doesntHave' => [
            'requirements' => ['instance'],
            'templates' => [
                'name' => 'scope^{methodname}^{identifier}',
                'multi_name' => 'scope^{methodname}',
            ],
        ],
        'has' => [
            'requirements' => ['instance'],
            'templates' => [
                'name' => 'scope^{methodname}^{identifier}',
                'multi_name' => 'scope^{methodname}',
            ],
        ],
    ],

];
