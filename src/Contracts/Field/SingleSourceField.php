<?php
/**
 * Single source field contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field;

use Laramore\Contracts\Field\Constraint\RelationConstraint;

interface SingleSourceField extends RelationField
{
    /**
     * Return the source of the relation.
     *
     * @return RelationConstraint
     */
    public function getSource(): RelationConstraint;
}
