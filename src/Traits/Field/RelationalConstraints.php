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

use Laramore\Contracts\Field\{
    Field, Constraint\IndexableConstraint
};
use Laramore\Fields\Constraint\BaseRelationalConstraint;

trait RelationalConstraints
{
    /**
     * Define a foreign constraint.
     *
     * @param  string              $name
     * @param IndexableConstraint $target
     * @param  Field|array<Field>  $fields
     * @return self
     */
    public function foreign(string $name=null, IndexableConstraint $target, $fields=[])
    {
        $this->needsToBeUnlocked();

        $constraint = $this->getConstraintHandler()->create(BaseRelationalConstraint::FOREIGN, $name, $fields);
        $constraint->setTarget($target);

        return $this;
    }
}
