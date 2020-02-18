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

    'common_configurations' => [
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
            'name_template' => '${methodname}^{identifier}Attribute',
            'multi_proxy_template' => '${methodname}Attribute',
        ],
        'set' => [
            'name_template' => '${methodname}^{identifier}Attribute',
            'multi_proxy_template' => '${methodname}Attribute',
        ],
        'reset' => [
            'name_template' => '${methodname}^{identifier}Attribute',
            'multi_proxy_template' => '${methodname}Attribute',
        ],
        'relate' => [
            'name_template' => '${identifier}',
        ],
        'where' => [
            'requirements' => ['instance'],
            'name_template' => 'scope^{$methodname}^{identifier}',
        ],
        'doesntHave' => [
            'requirements' => ['instance'],
            'name_template' => 'scope^{$methodname}^{identifier}',
        ],
        'has' => [
            'requirements' => ['instance'],
            'name_template' => 'scope^{$methodname}^{identifier}',
        ],
    ],

];
