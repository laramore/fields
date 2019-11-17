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

use Laramore\Fields\Field;
use Laramore\Fields\Constraint\{
    Primary, Index, Unique, Foreign
};

trait HasFieldConstraints
{
    /**
     * Constraints.
     *
     * @var array
     */
    protected $constraints = [];

    /**
     * Define a primary constraint.
     *
     * @param  string  $name
     * @param  string  $class
     * @param  integer $priority
     * @return self
     */
    public function primary(string $name=null, string $class=null, int $priority=Primary::MEDIUM_PRIORITY)
    {
        $this->needsToBeUnlocked();

        if (isset($this->constraints['primary'])) {
            throw new \LogicException("This field cannot have multiple primary constraints");
        }

        if (\is_null($class)) {
            $class = config('fields.constraints.types.primary.class');
        }

        $this->constraints['primary'] = $class::constraint([$this], $name, $priority);

        return $this;
    }

    /**
     * Define an index constraint.
     *
     * @param  string  $name
     * @param  string  $class
     * @param  integer $priority
     * @return self
     */
    public function index(string $name=null, string $class=null, int $priority=Index::MEDIUM_PRIORITY)
    {
        $this->needsToBeUnlocked();

        if (isset($this->constraints['index'])) {
            throw new \LogicException("This field cannot have multiple index constraints");
        }

        if (\is_null($class)) {
            $class = config('fields.constraints.types.index.class');
        }

        $this->constraints['index'] = $class::constraint([$this], $name, $priority);

        return $this;
    }

    /**
     * Define a unique constraint.
     *
     * @param  string  $name
     * @param  string  $class
     * @param  integer $priority
     * @return self
     */
    public function unique(string $name=null, string $class=null, int $priority=Unique::MEDIUM_PRIORITY)
    {
        $this->needsToBeUnlocked();

        if (isset($this->constraints['unique'])) {
            throw new \LogicException("This field cannot have multiple unique constraints");
        }

        if (\is_null($class)) {
            $class = config('fields.constraints.types.unique.class');
        }

        $this->constraints['unique'] = $class::constraint([$this], $name, $priority);

        return $this;
    }

    /**
     * Define a unique constraint.
     *
     * @param  string  $name
     * @param  string  $class
     * @param  integer $priority
     * @return self
     */
    public function foreign(Field $field, string $name=null, string $class=null, int $priority=Foreign::MEDIUM_PRIORITY)
    {
        $this->needsToBeUnlocked();

        if (isset($this->constraints['foreign'])) {
            throw new \LogicException("This field cannot have multiple foreign constraints");
        }

        if (\is_null($class)) {
            $class = config('fields.constraints.types.foreign.class');
        }

        $this->constraints['foreign'] = $class::constraint([$this, $field], $name, $priority);

        return $this;
    }

    protected function setConstraints()
    {
        $handler = $this->getMeta()->getConstraintHandler();

        foreach ($this->constraints as $constraint) {
            $handler->add($constraint);
        }
    }
}
