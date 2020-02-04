<?php
/**
 * Add management for multiple field constraints.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Traits\Field;

use Laramore\Fields\AttributeField;
use Laramore\Fields\Constraint\{
    Primary, Index, Unique, Foreign
};

trait HasMultipleFieldConstraints
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
            $class = config('field.constraints.types.primary.class');
        }

        if (\count($this->getFields()) === 1) {
            $this->getFields()[0]->primary(\func_get_args());
        }

        $this->constraints['primary'] = $class::constraint(\array_values($this->getFields()), $name, $priority);

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
            $class = config('field.constraints.types.index.class');
        }

        if (\count($this->getFields()) === 1) {
            $this->getFields()[0]->index(\func_get_args());
        }

        $this->constraints['index'] = $class::constraint(\array_values($this->getFields()), $name, $priority);

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

        if ($this->constraints['unique']) {
            throw new \LogicException("This field cannot have multiple unique constraints");
        }

        if (\is_null($class)) {
            $class = config('field.constraints.types.unique.class');
        }

        if (\count($this->getFields()) === 1) {
            $this->getFields()[0]->unique(\func_get_args());
        }

        $this->constraints['unique'] = $class::constraint(\array_values($this->getFields()), $name, $priority);

        return $this;
    }

    /**
     * Define a unique constraint.
     *
     * @param  string         $fieldName
     * @param  AttributeField $field
     * @param  string         $name
     * @param  string         $class
     * @param  integer        $priority
     * @return self
     */
    protected function foreign(string $fieldName, AttributeField $field, string $name=null, string $class=null, int $priority=Foreign::MEDIUM_PRIORITY)
    {
        $this->needsToBeUnlocked();

        $this->getField($fieldName)->foreign($field, $name, $class, $priority);

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
