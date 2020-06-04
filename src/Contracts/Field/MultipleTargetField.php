<?php
/**
 * Multiple target field contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field;

use Laramore\Contracts\Field\Constraint\RelationConstraint;

interface MultipleTargetField extends RelationField
{
    /**
     * Return the targets of the relation.
     *
     * @return array<RelationConstraint>
     */
    public function getTargets(): array;
}
