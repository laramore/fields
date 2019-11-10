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
        Laramore\Fields\BelongsToMany::class => [
            'type' => 'link',
        ],
        Laramore\Fields\Boolean::class => [
            'type' => 'boolean',
        ],
        Laramore\Fields\Char::class => [
            'type' => 'char',
        ],
        Laramore\Fields\Email::class => [
            'type' => 'email',
        ],
        Laramore\Fields\Enum::class => [
            'type' => 'enum',
        ],
        Laramore\Fields\Foreign::class => [
            'type' => 'composite',
        Laramore\Fields\HasMany::class => [
            'type' => 'link',
        ],
        Laramore\Fields\HasManyThrough::class => [
            'type' => 'link',
        ],
        Laramore\Fields\HasOne::class => [
            'type' => 'link',
        ],
        Laramore\Fields\Increment::class => [
            'type' => 'increment',
        ],
        Laramore\Fields\Integer::class => [
            'type' => 'integer',
        ],
        Laramore\Fields\ManyToMany::class => [
            'type' => 'composite',
        Laramore\Fields\MorphToOne::class => [
            'type' => 'composite',
        Laramore\Fields\OneToOne::class => [
            'type' => 'composite',
        ],
        Laramore\Fields\Password::class => [
            'type' => 'password',
        ],
        Laramore\Fields\PrimaryId::class => [
            'type' => 'primary_id',
        ],
        Laramore\Fields\Text::class => [
            'type' => 'text',
        ],
        Laramore\Fields\Timestamp::class => [
            'type' => 'timestamp',
        ],
    ],

];
