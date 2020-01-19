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

    'types' => [
        'primary' => [
            'class' => Laramore\Fields\Constraint\Primary::class,
        ],
        'index' => [
            'class' => Laramore\Fields\Constraint\Index::class,
        ],
        'unique' => [
            'class' => Laramore\Fields\Constraint\Unique::class,
        ],
        'foreign' => [
            'class' => Laramore\Fields\Constraint\Foreign::class,
        ],
    ],

];
