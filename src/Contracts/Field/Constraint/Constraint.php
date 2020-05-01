<?php
/**
 * Define a classic constraint contract.
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

interface Constraint extends Locked
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
}
