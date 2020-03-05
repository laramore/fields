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
     * @param  string $name
     * @return self
     */
    public function primary(string $name=null)
    {
        $this->needsToBeUnlocked();

        $this->getConstraintHandler()->createPrimary($name);

        return $this;
    }

    /**
     * Define a index constraint.
     *
     * @param  string $name
     * @return self
     */
    public function index(string $name=null)
    {
        $this->needsToBeUnlocked();

        $this->getConstraintHandler()->createIndex($name);

        return $this;
    }

    /**
     * Define a unique constraint.
     *
     * @param  string $name
     * @return self
     */
    public function unique(string $name=null)
    {
        $this->needsToBeUnlocked();

        $this->getConstraintHandler()->createUnique($name);

        return $this;
    }

    /**
     * Define a foreign constraint.
     *
     * @param  ConstraintedField $constrainedField
     * @param  string            $name
     * @return self
     */
    public function foreign(ConstraintedField $constrainedField, string $name=null)
    {
        $this->needsToBeUnlocked();

        $this->getConstraintHandler()->createForeign($constrainedField, $name);

        return $this;
    }
}
