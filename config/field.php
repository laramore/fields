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

    'templates' => [
        'name' => '_{name}',
        'attname' => '_{attname}',
        'method_owner' => '-{methodname}FieldValue',
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
        'belongs_to_many' => [
            'type' => 'link',
            'proxies' => [
                'attach' => [],
                'detach' => [],
                'sync' => [],
                'toggle' => [],
                'sync_without_detaching' => [],
                'update_existing_pivot' => [],
            ],
        ],
        'binary' => [
            'type' => 'boolean',
            'proxies' => [],
        ],
        'boolean' => [
            'type' => 'boolean',
            'proxies' => [
                'is' => [
                    'needs_value' => true,
                ],
                'is_not' => [
                    'needs_value' => true,
                ],
            ],
        ],
        'char' => [
            'type' => 'char',
            'proxies' => [
                'resize' => [],
            ],
        ],
        'date_time' => [
            'type' => 'datetime',
            'format' => 'Y-m-d H:i:s',
            'proxies' => [],
        ],
        'decimal' => [
            'type' => 'decimal',
            'types' =>  [
                'big' => 'big_decimal',
                'small' => 'small_decimal',
                'unsigned' => 'unsigned_decimal',
                'big_unsigned' => 'big_unsigned_decimal',
                'small_unsigned' => 'small_unsigned_decimal',
            ],
            'proxies' => [],
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
                'separator' => '@',
                'flags' => null,
            ]
        ],
        'enum' => [
            'type' => 'enum',
            'elements' => [
                'proxy_class' => Laramore\Proxies\EnumProxy::class,
                'proxies' => [
                    'is' => [
                        'templates' => [
                            'name' => '-{methodname}^{elementname}',
                        ],
                        'needs_value' => true,
                    ]
                ],
            ],
            'proxies' => [
                'get_elements' => [
                    'templates' => [
                        'name' => 'get^{identifier}Elements',
                    ],
                ],
                'get_elements_value' => [
                    'templates' => [
                        'name' => 'get*{identifier}',
                    ],
                ],
                'is' => [
                    'needs_value' => true,
                ],
                'is_not' => [
                    'needs_value' => true,
                ],
            ],
        ],
        'has_many' => [
            'type' => 'link',
            'proxies' => [
                'attach' => [],
                'detach' => [],
                'sync' => [],
                'toggle' => [],
                'sync_without_detaching' => [],
                'update_existing_pivot' => [],
            ],
        ],
        'has_many_through' => [
            'type' => 'link',
            'proxies' => [
                'attach' => [],
                'detach' => [],
                'sync' => [],
                'toggle' => [],
                'sync_without_detaching' => [],
                'update_existing_pivot' => [],
            ],
        ],
        'has_one' => [
            'type' => 'link',
            'proxies' => [],
        ],
        'increment' => [
            'type' => 'increment',
            'proxies' => [
                'increment' => [],
            ],
        ],
        'integer' => [
            'type' => 'integer',
            'types' =>  [
                'big' => 'big_integer',
                'small' => 'small_integer',
                'unsigned' => 'unsigned_integer',
                'big_unsigned' => 'big_unsigned_integer',
                'small_unsigned' => 'small_unsigned_integer',
            ],
            'proxies' => [],
        ],
        'json' => [
            'type' => 'json',
            'proxies' => [],
        ],
        'many_to_many' => [
            'type' => 'composed',
            'pivot_namespace' => 'App\\Pivots',
            'fields' => [
                'reversed' => Laramore\Fields\BelongsToMany::class,
            ],
            'templates' => [
                'reversed' => '+{modelname}',
                'pivot' => 'pivot',
                'reversed_pivot' => 'pivot',
                'self_reversed' => 'reversed_+{name}',
                'self_pivot_reversed' => 'reversed_+{modelname}',
            ],
            'proxies' => [
                'attach' => [],
            ],
        ],
        'one_to_many' => [
            'type' => 'composed',
            'fields' => [
                'id' => Laramore\Fields\Integer::class,
                'reversed' => Laramore\Fields\HasMany::class,
            ],
            'templates' => [
                'id' => '${name}_${identifier}',
                'reversed' => '+{modelname}',
                'self_reversed' => 'reversed_+{name}',
            ],
            'proxies' => [],
        ],
        'one_to_one' => [
            'type' => 'composed',
            'fields' => [
                'id' => Laramore\Fields\UniqueId::class,
                'reversed' => Laramore\Fields\HasOne::class,
            ],
            'templates' => [
                'id' => '${name}_${identifier}',
                'reversed' => '+{modelname}',
                'self_reversed' => 'reversed_+{name}',
            ],
            'proxies' => [],
        ],
        'password' => [
            'type' => 'password',
            'proxies' => [
                'resize' => [],
                'hash' => [],
                'is_correct' => [
                    'templates' => [
                        'name' => 'is^{identifier}Correct',
                    ],
                    'needs_value' => true,
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
        'unique_id' => [
            'type' => 'integer',
            'types' =>  [
                'big' => 'big_integer',
                'small' => 'small_integer',
                'unsigned' => 'unsigned_integer',
                'big_unsigned' => 'big_unsigned_integer',
                'small_unsigned' => 'small_unsigned_integer',
            ],
            'proxies' => [],
        ],
        'text' => [
            'type' => 'text',
            'proxies' => [],
        ],
        'timestamp' => [
            'type' => 'timestamp',
            'format' => 'timestamp',
            'proxies' => [],
        ],
        'uri' => [
            'type' => 'uri',
            'proxies' => [
                'fix' => [],
            ],
            'patterns' => [
                'identifier' => '/^\S+$/',
                'protocol' => '/^\S+:\/{0,2}$/',
                'uri' => '/^\S+:\/{0,2}\S+$/',
                'separator' => '',
                'flags' => null,
            ]
        ],

        'body' => [
            'type' => 'body',
            'proxies' => [],
        ],
        'name' => [
            'type' => 'composed',
            'fields' => [
                'firstname' => [
                    Laramore\Fields\Body::class,
                    ['visible', 'fillable', 'required', 'title'],
                ],
                'lastname' => [
                    Laramore\Fields\Body::class,
                    ['visible', 'fillable', 'required', 'uppercase'],
                ],
            ],
            'templates' => [
                'firstname' => 'firstname',
                'lastname' => 'lastname',
            ],
            'proxies' => [],
        ],
    ],

];
