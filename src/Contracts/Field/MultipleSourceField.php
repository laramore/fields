<?php
/**
 * Multiple source field contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field;

use Laramore\Contracts\Field\Constraint\RelationConstraint;

interface MultipleSourceField extends RelationField
{
    /**
     * Return the sources of the relation.
     *
     * @return RelationConstraint
     */
    public function getSources(): RelationConstraint;
}
