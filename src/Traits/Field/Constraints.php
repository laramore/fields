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

use Laramore\Contracts\Field\Constraint\ConstraintedField;

trait Constraints
{
    /**
     * Define a primary constraint.
     *
     * @param  string                                     $name
     * @param  ConstraintedField|array<ConstraintedField> $fields
     * @return self
     */
    public function primary(string $name=null, $fields=[])
    {
        $this->needsToBeUnlocked();

        $this->getConstraintHandler()->createPrimary($name, $fields);

        return $this;
    }

    /**
     * Define a index constraint.
     *
     * @param  string                                     $name
     * @param  ConstraintedField|array<ConstraintedField> $fields
     * @return self
     */
    public function index(string $name=null, $fields=[])
    {
        $this->needsToBeUnlocked();

        $this->getConstraintHandler()->createIndex($name, $fields);

        return $this;
    }

    /**
     * Define a unique constraint.
     *
     * @param  string                                     $name
     * @param  ConstraintedField|array<ConstraintedField> $fields
     * @return self
     */
    public function unique(string $name=null, $fields=[])
    {
        $this->needsToBeUnlocked();

        $this->getConstraintHandler()->createUnique($name, $fields);

        return $this;
    }

    /**
     * Define a foreign constraint.
     *
     * @param  ConstraintedField|array<ConstraintedField> $fields
     * @param  string                                     $name
     * @return self
     */
    public function foreign(string $name=null, $fields=[])
    {
        $this->needsToBeUnlocked();

        $this->getConstraintHandler()->createForeign($name, $fields);

        return $this;
    }
}
