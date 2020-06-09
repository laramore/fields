<?php
/**
 * Add management for field constraints.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Traits\Field;

use Laramore\Contracts\Field\Field;
use Laramore\Fields\Constraint\BaseConstraint;

trait ForeignConstraints
{
    /**
     * Define a foreign constraint.
     *
     * @param  string             $name
     * @param  Field|array<Field> $fields
     * @return self
     */
    public function foreign(string $name=null, $fields=[])
    {
        $this->needsToBeUnlocked();

        $this->getConstraintHandler()->create(BaseConstraint::FOREIGN, $name, $fields);

        return $this;
    }
}
