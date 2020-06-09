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

trait IndexableConstraints
{
    /**
     * Define a primary constraint.
     *
     * @param  string             $name
     * @param  Field|array<Field> $fields
     * @return self
     */
    public function primary(string $name=null, $fields=[])
    {
        $this->needsToBeUnlocked();

        $this->getConstraintHandler()->create(BaseConstraint::PRIMARY, $name, $fields);

        return $this;
    }

    /**
     * Define a index constraint.
     *
     * @param  string             $name
     * @param  Field|array<Field> $fields
     * @return self
     */
    public function index(string $name=null, $fields=[])
    {
        $this->needsToBeUnlocked();

        $this->getConstraintHandler()->create(BaseConstraint::INDEX, $name, $fields);

        return $this;
    }

    /**
     * Define a unique constraint.
     *
     * @param  string             $name
     * @param  Field|array<Field> $fields
     * @return self
     */
    public function unique(string $name=null, $fields=[])
    {
        $this->needsToBeUnlocked();

        $this->getConstraintHandler()->create(BaseConstraint::UNIQUE, $name, $fields);

        return $this;
    }
}
