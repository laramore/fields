<?php
/**
 * Single target field contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field;

use Laramore\Contracts\Field\Constraint\RelationConstraint;

interface SingleTargetField extends RelationField
{
    /**
     * Return the target of the relation.
     *
     * @return RelationConstraint
     */
    public function getTarget(): RelationConstraint;
}
