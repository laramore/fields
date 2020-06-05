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

use Laramore\Contracts\Field\Constraint\Constraint;

interface SingleSourceField extends RelationField
{
    /**
     * Return the source of the relation.
     *
     * @return Constraint
     */
    public function getSource(): Constraint;
}
