<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default constraints
    |--------------------------------------------------------------------------
    |
    | This option defines the default constraints used in fields.
    |
    */

    'manager' => Laramore\Fields\Constraint\ConstraintManager::class,

    'classes' => [
        'primary' => Laramore\Fields\Constraint\Primary::class,
        'index' => Laramore\Fields\Constraint\Index::class,
        'unique' => Laramore\Fields\Constraint\Unique::class,
        'foreign' => Laramore\Fields\Constraint\Foreign::class,
    ],

    'configurations' => [],
];
