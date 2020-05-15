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
        'get_default' => [
            'static' => true,
        ],
        'get' => [
            'templates' => [
                'name' => '-{methodname}-^{identifier}Attribute',
                'multi_name' => '-{methodname}Attribute',
            ],
        ],
        'set' => [
            'templates' => [
                'name' => '-{methodname}-^{identifier}Attribute',
                'multi_name' => '-{methodname}Attribute',
            ],
        ],
        'reset' => [
            'templates' => [
                'name' => '-{methodname}-^{identifier}Attribute',
                'multi_name' => '-{methodname}Attribute',
            ],
        ],
        'relate' => [
            'templates' => [
                'name' => '-{identifier}',
            ],
        ],
        'where' => [
            'templates' => [
                'name' => 'scope-^{methodname}-^{identifier}',
                'multi_name' => 'scope-^{methodname}',
            ],
        ],
        'doesnt_have' => [
            'templates' => [
                'name' => 'scope-^{methodname}-^{identifier}',
                'multi_name' => 'scope-^{methodname}',
            ],
        ],
        'has' => [
            'templates' => [
                'name' => 'scope-^{methodname}-^{identifier}',
                'multi_name' => 'scope-^{methodname}',
            ],
        ],
    ],

];
