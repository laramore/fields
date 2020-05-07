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
        'index' => Laramore\Fields\Constraint\Index::class,
        'unique' => Laramore\Fields\Constraint\Unique::class,
        'foreign' => Laramore\Fields\Constraint\Foreign::class,
        'primary' => Laramore\Fields\Constraint\Primary::class,
    ],

    'configurations' => [
        'index' => [
            'type' => Laramore\Fields\Constraint\BaseConstraint::INDEX,
        ],
        'unique' => [
            'type' => Laramore\Fields\Constraint\BaseConstraint::UNIQUE,
        ],
        'foreign' => [
            'type' => Laramore\Fields\Constraint\BaseConstraint::FOREIGN,
        ],
        'primary' => [
            'type' => Laramore\Fields\Constraint\BaseConstraint::PRIMARY,
        ],
    ],
];
