<?php

use Laramore\Proxies\ProxyHandler;

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

    /*
    |--------------------------------------------------------------------------
    | Default proxies
    |--------------------------------------------------------------------------
    |
    | This option defines all proxy configurations.
    |
    */

    'proxies' => [
        'enabled' => true,

        'manager' => Laramore\Proxies\ProxyManager::class,

        'class' => Laramore\Proxies\FieldProxy::class,

        'configurations' => [
            'targets' => [ProxyHandler::MODEL_TYPE],
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
            'proxies' => [
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
            ],
        ],
        Laramore\Fields\Boolean::class => [
            'type' => 'boolean',
            'type' => 'link',
            'proxies' => [
                'is' => [
                    'requirements' => ['value'],
                ],
                'isNot' => [
                    'requirements' => ['value'],
                ],
            ],
        ],
        Laramore\Fields\Char::class => [
            'type' => 'char',
            'proxies' => [
                'resize' => [],
            ],
        ],
        Laramore\Fields\Email::class => [
            'type' => 'email',
            'proxies' => [
                'fix' => [],
            ],
            'patterns' => [
                'username' => '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*/iD',
                'domain' => '/^(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD',
                'email' => '/^(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){255,})(?!(?:(?:\x22?\x5C[\x00-\x7E]\x22?)|(?:\x22?[^\x5C\x22]\x22?)){65,}@)(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22))(?:\.(?:(?:[\x21\x23-\x27\x2A\x2B\x2D\x2F-\x39\x3D\x3F\x5E-\x7E]+)|(?:\x22(?:[\x01-\x08\x0B\x0C\x0E-\x1F\x21\x23-\x5B\x5D-\x7F]|(?:\x5C[\x00-\x7F]))*\x22)))*@(?:(?:(?!.*[^.]{64,})(?:(?:(?:xn--)?[a-z0-9]+(?:-[a-z0-9]+)*\.){1,126}){1,}(?:(?:[a-z][a-z0-9]*)|(?:(?:xn--)[a-z0-9]+))(?:-[a-z0-9]+)*)|(?:\[(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){7})|(?:(?!(?:.*[a-f0-9][:\]]){7,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,5})?)))|(?:(?:IPv6:(?:(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){5}:)|(?:(?!(?:.*[a-f0-9]:){5,})(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3})?::(?:[a-f0-9]{1,4}(?::[a-f0-9]{1,4}){0,3}:)?)))?(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))(?:\.(?:(?:25[0-5])|(?:2[0-4][0-9])|(?:1[0-9]{2})|(?:[1-9]?[0-9]))){3}))\]))$/iD',
            ]
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
            'proxies' => [
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
            ],
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
            'proxies' => [],
        ],
        Laramore\Fields\HasMany::class => [
            'type' => 'link',
            'proxies' => [
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
            ],
        ],
        Laramore\Fields\HasManyThrough::class => [
            'type' => 'link',
            'proxies' => [
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
            ],
        ],
        Laramore\Fields\HasOne::class => [
            'type' => 'link',
            'proxies' => [],
        ],
        Laramore\Fields\Increment::class => [
            'type' => 'increment',
            'proxies' => [
                'increment' => [
                    'requirements' => ['instance', 'value'],
                ],
            ],
        ],
        Laramore\Fields\Integer::class => [
            'type' => 'integer',
            'unsigned_type' => 'unsigned_integer',
            'proxies' => [],
        ],
        Laramore\Fields\ManyToMany::class => [
            'type' => 'composite',
            'fields' => [],
            'links' => [
                'reversed' => Laramore\Fields\BelongsToMany::class,
            ],
            'field_name_template' => '${name}_${fieldname}',
            'link_name_template' => '+{modelname}',
            'proxies' => [],
        ],
        Laramore\Fields\MorphToOne::class => [
            'type' => 'composite',
            'fields' => [],
            'links' => [
                'reversed' => Laramore\Fields\BelongsToMany::class,
            ],
            'field_name_template' => '${name}_${fieldname}',
            'link_name_template' => '+{modelname}',
            'proxies' => [],
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
            'proxies' => [],
        ],
        Laramore\Fields\Password::class => [
            'type' => 'password',
            'proxies' => [
                'resize' => [],
                'hash' => [],
                'isCorrect' => [
                    'name_template' => 'is^{fieldname}Correct',
                    'requirements' => ['value'],
                ],
            ],
            'patterns' => [
                'min_max_part' => '(?=\S{$min,$max})',
                'one_lowercase_part' => '(?=\S*[a-z])',
                'one_uppercase_part' => '(?=\S*[A-Z])',
                'one_number_part' => '(?=\S*[\d])',
                'one_special_part' => '(?=\S*[\W])',
            ]
        ],
        Laramore\Fields\PrimaryId::class => [
            'type' => 'primary_id',
            'proxies' => [],
        ],
        Laramore\Fields\Text::class => [
            'type' => 'text',
            'proxies' => [],
        ],
        Laramore\Fields\Timestamp::class => [
            'type' => 'timestamp',
            'proxies' => [],
        ],
    ],

];
