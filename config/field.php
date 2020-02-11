<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Name templates for the fields generation
    |--------------------------------------------------------------------------
    |
    | This option defines the template used to generate the name and the
    | attribute name, if existant, of a field.
    |
    */

    'name_template' => '_{name}',
    'attname_template' => '_{attname}',

    /*
    |--------------------------------------------------------------------------
    | Default fields
    |--------------------------------------------------------------------------
    |
    | This option defines the default fields.
    |
    */

    'configurations' => [
        'belongs_to_many' => [
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
        'boolean' => [
            'type' => 'boolean',
            'proxies' => [
                'is' => [
                    'requirements' => ['value'],
                ],
                'isNot' => [
                    'requirements' => ['value'],
                ],
            ],
        ],
        'char' => [
            'type' => 'char',
            'proxies' => [
                'resize' => [],
            ],
        ],
        'email' => [
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
        'enum' => [
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
        'foreign' => [
            'type' => 'composite',
            'attributes' => [
                'id' => Laramore\Fields\Integer::class,
            ],
            'links' => [
                'reversed' => Laramore\Fields\HasMany::class,
            ],
            'self_reversed_name_template' => 'reversed_+{name}',
            'attribute_name_template' => '${name}_${fieldname}',
            'link_name_template' => '+{modelname}',
            'proxies' => [],
        ],
        'has_many' => [
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
        'has_many_through' => [
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
        'has_one' => [
            'type' => 'link',
            'proxies' => [],
        ],
        'increment' => [
            'type' => 'increment',
            'proxies' => [
                'increment' => [
                    'requirements' => ['instance', 'value'],
                ],
            ],
        ],
        'integer' => [
            'type' => 'integer',
            'unsigned_type' => 'unsigned_integer',
            'proxies' => [],
        ],
        'many_to_many' => [
            'type' => 'composite',
            'attributes' => [],
            'links' => [
                'reversed' => Laramore\Fields\BelongsToMany::class,
            ],
            'pivot_name_template' => 'pivot',
            'reversed_pivot_name_template' => 'pivot',
            'self_reversed_name_template' => 'reversed_+{name}',
            'self_pivot_reversed_name_template' => 'reversed_+{modelname}',
            'attribute_name_template' => '${name}_${fieldname}',
            'link_name_template' => '+{modelname}',
            'proxies' => [
                'attach' => [
                    'requirements' => ['instance'],
                ],
            ],
        ],
        'morph_to_one' => [
            'type' => 'composite',
            'attributes' => [],
            'links' => [
                'reversed' => Laramore\Fields\BelongsToMany::class,
            ],
            'attribute_name_template' => '${name}_${fieldname}',
            'link_name_template' => '+{modelname}',
            'proxies' => [],
        ],
        'one_to_one' => [
            'type' => 'composite',
            'attributes' => [
                'id' => Laramore\Fields\Integer::class,
            ],
            'links' => [
                'reversed' => Laramore\Fields\HasOne::class,
            ],
            'attribute_name_template' => '${name}_${fieldname}',
            'link_name_template' => '+{modelname}',
            'proxies' => [],
        ],
        'password' => [
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
        'primary_id' => [
            'type' => 'primary_id',
            'proxies' => [],
        ],
        'text' => [
            'type' => 'text',
            'proxies' => [],
        ],
        'timestamp' => [
            'type' => 'timestamp',
            'proxies' => [],
        ],
    ],

];
