<?php

return [

    /*
        |--------------------------------------------------------------------------
        | Default rules
        |--------------------------------------------------------------------------
        |
        | This option defines the default rules used in fields.
        |
    */

    'configurations' => [
        'Laramore\\Fields\\BelongsToMany' => [
            'type' => 'link',
        ],
        'Laramore\\Fields\\Boolean' => [
            'type' => 'boolean',
        ],
        'Laramore\\Fields\\Char' => [
            'type' => 'char',
        ],
        'Laramore\\Fields\\Email' => [
            'type' => 'email',
        ],
        'Laramore\\Fields\\Enum' => [
            'type' => 'enum',
        ],
        'Laramore\\Fields\\Foreign' => [
            'type' => 'composite',
        ],
        'Laramore\\Fields\\HasMany' => [
            'type' => 'link',
        ],
        'Laramore\\Fields\\HasManyThrough' => [
            'type' => 'link',
        ],
        'Laramore\\Fields\\HasOne' => [
            'type' => 'link',
        ],
        'Laramore\\Fields\\Increment' => [
            'type' => 'increment',
        ],
        'Laramore\\Fields\\Integer' => [
            'type' => 'integer',
        ],
        'Laramore\\Fields\\ManyToMany' => [
            'type' => 'composite',
        ],
        'Laramore\\Fields\\MorphToOne' => [
            'type' => 'composite',
        ],
        'Laramore\\Fields\\OneToOne' => [
            'type' => 'composite',
        ],
        'Laramore\\Fields\\Password' => [
            'type' => 'password',
        ],
        'Laramore\\Fields\\PrimaryId' => [
            'type' => 'primary_id',
        ],
        'Laramore\\Fields\\Text' => [
            'type' => 'text',
        ],
        'Laramore\\Fields\\Timestamp' => [
            'type' => 'timestamp',
        ],
    ],

];
