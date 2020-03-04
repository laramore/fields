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
    Eloquent\Builder, Field\Constraint\ConstraintedField
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
     * Define a shared field.
     * Usefull to link this attribute as fk to another.
     *
     * @param AttributeField $field
     * @return self
     */
    public function sharedField(AttributeField $field);

    /**
     * Add an operation to a query builder.
     *
     * @param Builder $builder
     * @param string  $operation
     * @param mixed   ...$params
     * @return Builder
     */
    public function addBuilderOperation(Builder $builder, string $operation, ...$params): Builder;
}
