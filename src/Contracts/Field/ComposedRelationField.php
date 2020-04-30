<?php
/**
 * Relation field contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field;

use Laramore\Contracts\Field\AttributeField;

interface ComposedRelationField extends RelationField
{
    /**
     * Return the attributes that is pointed by this foreign relation.
     *
     * @return array<AttributeField>
     */
    public function getTargetAttributes(): array;

    /**
     * Return the attributes that points to another.
     *
     * @return array<AttributeField>
     */
    public function getSourceAttributes(): array;
}
