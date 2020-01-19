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

    'enabled' => true,

    'manager' => \Laramore\Fields\Proxy\ProxyManager::class,

    'class' => \Laramore\Fields\Proxy\Proxy::class,

    'configurations' => [
        'targets' => [\Laramore\Fields\Proxy\ProxyHandler::MODEL_TYPE],
        'requirements' => [],
        'name_template' => '${methodname}^{fieldname}',
    ],

    'common' => [
        'relate' => [
            'name_template' => '${fieldname}',
            'requirements' => ['instance'],
        ],
        'where' => [
            'requirements' => ['instance'],
            'targets' => [\Laramore\Fields\Proxy\ProxyHandler::BUILDER_TYPE],
        ],
        'doesntHave' => [
            'requirements' => ['instance'],
            'targets' => [\Laramore\Fields\Proxy\ProxyHandler::BUILDER_TYPE],
        ],
        'has' => [
            'requirements' => ['instance'],
            'targets' => [\Laramore\Fields\Proxy\ProxyHandler::BUILDER_TYPE],
        ],
    ],

];
