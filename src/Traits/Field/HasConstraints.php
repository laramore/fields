<?php
/**
 * Add management for field constraints.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Traits\Field;

use Laramore\Fields\Constraint\FieldConstraintHandler;

trait HasConstraints
{
    use Constraints;

    /**
     * Constraint handler.
     *
     * @var FieldConstraintHandler
     */
    protected $constraintHandler;

    /**
     * Create a Constraint handler for this meta.
     *
     * @return void
     */
    protected function setConstraintHandler()
    {
        $this->constraintHandler = new FieldConstraintHandler($this);
    }

    /**
     * Return the relation handler for this meta.
     *
     * @return FieldConstraintHandler
     */
    public function getConstraintHandler(): FieldConstraintHandler
    {
        if ($this->isOwned()) {
            return $this->getMeta()->getConstraintHandler()->getFieldHandler($this->getName());
        }

        return $this->constraintHandler;
    }

    /**
     * Own this constraint handler.
     *
     * @return void
     */
    protected function owned()
    {
        $this->getMeta()->getConstraintHandler()->addFieldHandler($this->constraintHandler);

        unset($this->constraintHandler);
    }
}
