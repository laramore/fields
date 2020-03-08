<?php
/**
 * Define a primary id field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Laramore\Contracts\Field\Constraint\PrimaryField;
use Laramore\Fields\Constraint\PrimaryConstraintHandler;

class PrimaryId extends Increment implements PrimaryField
{
    /**
     * Create a Constraint handler for this meta.
     *
     * @return void
     */
    protected function setConstraintHandler()
    {
        $this->constraintHandler = new PrimaryConstraintHandler($this);
    }

    /**
     * Return the relation handler for this meta.
     *
     * @return PrimaryConstraintHandler
     */
    public function getConstraintHandler(): PrimaryConstraintHandler
    {
        return parent::getConstraintHandler();
    }

    /**
     * Define a primary constraint.
     *
     * @param  string $name
     * @return self
     */
    public function primary(string $name=null)
    {
        $this->needsToBeUnlocked();

        $this->getConstraintHandler()->getPrimary()->setName($name);

        return $this;
    }
}
