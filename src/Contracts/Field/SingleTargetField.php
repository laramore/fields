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

use Laramore\Contracts\Field\Constraint\Constraint;

interface SingleTargetField extends RelationField
{
    /**
     * Return the target of the relation.
     *
     * @return Constraint
     */
    public function getTarget(): Constraint;
}
