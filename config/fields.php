<?php

use Laramore\Proxies\ProxyHandler;

$commonProxies = [
    'relate' => [
        'name_template' => '${fieldname}',
        'requirements' => ['instance'],
    ],
    'where' => [
        'requirements' => ['instance'],
        'targets' => [ProxyHandler::BUILDER_TYPE],
    ],
    'whereNull' => [
        'name_template' => 'doesntHave^{fieldname}',
        'requirements' => ['instance'],
        'targets' => [ProxyHandler::BUILDER_TYPE],
    ],
    'whereNotNull' => [
        'name_template' => 'has^{fieldname}',
        'requirements' => ['instance'],
        'targets' => [ProxyHandler::BUILDER_TYPE],
    ],
];

return [

    /*
    |--------------------------------------------------------------------------
    | Default constraints
    |--------------------------------------------------------------------------
    |
    | This option defines the default constraints used in fields.
    |
    */

    'constraints' => [
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
        ]
    ],

    'proxies' => [
        'enabled' => true,

        'manager' => Laramore\Proxies\ProxyManger::class,

        'default' => [
            'targets' => [ProxyHandler::MODEL_TYPE],
            'requirements' => [],
            'name_template' => '${methodname}^{fieldname}',
        ],

        'classes' => [
            'field' => Laramore\Proxies\FieldProxy::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default fields
    |--------------------------------------------------------------------------
    |
    | This option defines the default fields.
    |
    */

    'configurations' => [
        Laramore\Fields\BelongsToMany::class => [
            'type' => 'link',
            'proxies' => \array_merge($commonProxies, [
                'attach' => [
                    'requirements' => ['instance'],
                ],
                'detach' => [
                    'requirements' => ['instance'],
                ],
                'sync' => [
                    'requirements' => ['instance'],
                ],
                'toggle' => [
                    'requirements' => ['instance'],
                ],
                'syncWithoutDetaching' => [
                    'requirements' => ['instance'],
                ],
                'updateExistingPivot' => [
                    'requirements' => ['instance'],
                ],
            ]),
        ],
        Laramore\Fields\Boolean::class => [
            'type' => 'boolean',
            'type' => 'link',
            'proxies' => \array_merge($commonProxies, [
                'is' => [
                    'requirements' => ['value'],
                ],
                'isNot' => [
                    'requirements' => ['value'],
                ],
            ]),
        ],
        Laramore\Fields\Char::class => [
            'type' => 'char',
            'proxies' => \array_merge($commonProxies, [
                'resize' => [],
            ]),
        ],
        Laramore\Fields\Email::class => [
            'type' => 'email',
            'proxies' => \array_merge($commonProxies, [
                'fix' => [],
            ]),
        ],
        Laramore\Fields\Enum::class => [
            'type' => 'enum',
            'elements' => [
                'proxy' => [
                    'enabled' => true,
                    'name_template' => '${methodname}^{elementname}',
                    'requirements' => ['value'],
                ],
            ],
            'proxies' => \array_merge($commonProxies, [
                'getElements' => [
                    'name_template' => 'get^{fieldname}Elements',
                ],
                'getElementsValue' => [
                    'name_template' => 'get*{fieldname}',
                ],
                'is' => [
                    'requirements' => ['value'],
                ],
                'isNot' => [
                    'requirements' => ['value'],
                ],
            ]),
        ],
        Laramore\Fields\Foreign::class => [
            'type' => 'composite',
            'fields' => [
                'id' => [
                    Laramore\Fields\Integer::class,
                    ['visible', 'fillable', 'not_zero', 'unsigned', 'require_sign'],
                ],
            ],
            'links' => [
                'reversed' => Laramore\Fields\HasMany::class,
            ],
            'field_name_template' => '${name}_${fieldname}',
            'link_name_template' => '+{modelname}',
            'proxies' => \array_merge($commonProxies, []),
        ],
        Laramore\Fields\HasMany::class => [
            'type' => 'link',
            'proxies' => \array_merge($commonProxies, [
                'attach' => [
                    'requirements' => ['instance'],
                ],
                'detach' => [
                    'requirements' => ['instance'],
                ],
                'sync' => [
                    'requirements' => ['instance'],
                ],
                'toggle' => [
                    'requirements' => ['instance'],
                ],
                'syncWithoutDetaching' => [
                    'requirements' => ['instance'],
                ],
                'updateExistingPivot' => [
                    'requirements' => ['instance'],
                ],
            ]),
        ],
        Laramore\Fields\HasManyThrough::class => [
            'type' => 'link',
            'proxies' => \array_merge($commonProxies, [
                'attach' => [
                    'requirements' => ['instance'],
                ],
                'detach' => [
                    'requirements' => ['instance'],
                ],
                'sync' => [
                    'requirements' => ['instance'],
                ],
                'toggle' => [
                    'requirements' => ['instance'],
                ],
                'syncWithoutDetaching' => [
                    'requirements' => ['instance'],
                ],
                'updateExistingPivot' => [
                    'requirements' => ['instance'],
                ],
            ]),
        ],
        Laramore\Fields\HasOne::class => [
            'type' => 'link',
            'proxies' => \array_merge($commonProxies, []),
        ],
        Laramore\Fields\Increment::class => [
            'type' => 'increment',
            'proxies' => \array_merge($commonProxies, [
                'increment' => [
                    'requirements' => ['instance', 'value'],
                ],
            ]),
        ],
        Laramore\Fields\Integer::class => [
            'type' => 'integer',
            'unsigned_type' => 'unsigned_integer',
            'proxies' => \array_merge($commonProxies, []),
        ],
        Laramore\Fields\ManyToMany::class => [
            'type' => 'composite',
            'fields' => [],
            'links' => [
                'reversed' => Laramore\Fields\BelongsToMany::class,
            ],
            'field_name_template' => '${name}_${fieldname}',
            'link_name_template' => '+{modelname}',
            'proxies' => \array_merge($commonProxies, []),
        ],
        Laramore\Fields\MorphToOne::class => [
            'type' => 'composite',
            'fields' => [],
            'links' => [
                'reversed' => Laramore\Fields\BelongsToMany::class,
            ],
            'field_name_template' => '${name}_${fieldname}',
            'link_name_template' => '+{modelname}',
            'proxies' => \array_merge($commonProxies, []),
        ],
        Laramore\Fields\OneToOne::class => [
            'type' => 'composite',
            'fields' => [
                'id' => [
                    Laramore\Fields\Integer::class,
                    ['visible', 'fillable', 'not_zero', 'unsigned', 'require_sign'],
                ],
            ],
            'links' => [
                'reversed' => Laramore\Fields\HasOne::class,
            ],
            'field_name_template' => '${name}_${fieldname}',
            'link_name_template' => '+{modelname}',
            'proxies' => \array_merge($commonProxies, []),
        ],
        Laramore\Fields\Password::class => [
            'type' => 'password',
            'proxies' => \array_merge($commonProxies, [
                'resize' => [],
                'hash' => [],
                'isCorrect' => [
                    'name_template' => 'is^{fieldname}Correct',
                    'requirements' => ['value'],
                ],
            ]),
        ],
        Laramore\Fields\PrimaryId::class => [
            'type' => 'primary_id',
            'proxies' => \array_merge($commonProxies, []),
        ],
        Laramore\Fields\Text::class => [
            'type' => 'text',
            'proxies' => \array_merge($commonProxies, []),
        ],
        Laramore\Fields\Timestamp::class => [
            'type' => 'timestamp',
            'proxies' => \array_merge($commonProxies, []),
        ],
    ],

];
