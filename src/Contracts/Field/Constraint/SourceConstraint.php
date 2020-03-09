<?php
/**
 * Define a targetable constraint contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field\Constraint;

use Laramore\Contracts\{
    Locked, Field\AttributeField
};

interface SourceConstraint extends Locked
{
    /**
     * Return the constraint name.
     *
     * @return string
     */
    public function getConstraintType(): string;

    /**
     * Return the main attirbute
     *
     * @return AttributeField
     */
    public function getMainAttribute(): AttributeField;

    /**
     * Return all concerned attribute fields.
     *
     * @return array
     */
    public function getAttributes(): array;

    /**
     * Indicate if this constraint is composed of multiple fields.
     *
     * @return boolean
     */
    public function isComposed(): bool;

    /**
     * Return the attributes that points to another.
     *
     * @return array<AttributeField>
     */
    public function getSourceAttributes(): array;

    /**
     * Return the attributes that is pointed by this foreign relation.
     *
     * @return array<AttributeField>
     */
    public function getTargetAttributes(): array;

    /**
     * Return the attribute that points to another.
     *
     * @return AttributeField
     */
    public function getSourceAttribute(): AttributeField;

    /**
     * Return the attribute that is pointed by this foreign relation.
     *
     * @return AttributeField
     */
    public function getTargetAttribute(): AttributeField;
}
