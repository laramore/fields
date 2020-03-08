<?php
/**
 * Define an attribute field contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field;

use Laramore\Contracts\{
    Eloquent\LaramoreBuilder, Field\Constraint\ConstraintedField
};

interface AttributeField extends Field, ConstraintedField
{
    /**
     * Parse the attribute name.
     *
     * @param  string $attname
     * @return string
     */
    public static function parseAttname(string $attname): string;

    /**
     * Get the attribute name.
     *
     * @return string
     */
    public function getAttname(): string;

    /**
     * Add an operation to a query builder.
     *
     * @param LaramoreBuilder $builder
     * @param string          $operation
     * @param mixed           ...$params
     * @return LaramoreBuilder
     */
    public function addBuilderOperation(LaramoreBuilder $builder, string $operation, ...$params): LaramoreBuilder;
}