<?php
/**
 * Define a relation constraint contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field\Constraint;

use Laramore\Contracts\Field\AttributeField;

interface RelationConstraint extends Constraint
{
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
