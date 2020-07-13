<?php
/**
 * Handle all observers for a specific class.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Fields\Constraint;

use Laramore\Contracts\{
    Owned, Configured
};
use Laramore\Contracts\Field\{
    Field, Constraint\Constraint, Constraint\RelationalConstraint
};
use Laramore\Observers\BaseObserver;
use Laramore\Traits\IsOwned;

class FieldConstraintHandler extends BaseConstraintHandler implements Configured, Owned
{
    use IsOwned;

    /**
     * The observable class.
     *
     * @var string
     */
    protected $observableClass = Field::class;

    /**
     * The observer class to use to generate.
     *
     * @var string
     */
    protected $observerClass = Constraint::class;

    /**
     * Field field.
     *
     * @var Field
     */
    protected $field;

    /**
     * Create a field handler for a specific field.
     *
     * @param Field $field
     */
    public function __construct(Field $field)
    {
        $this->observableClass = \get_class($field);
        $this->field = $field;
    }

    /**
     * Return the field field.
     *
     * @return Field
     */
    public function getField(): Field
    {
        return $this->field;
    }

    /**
     * Add an observer for a specific model event.
     *
     * @param BaseObserver $constraint
     * @return self
     */
    public function add(BaseObserver $constraint)
    {
        /** @var BaseConstraint $constraint */
        parent::add($constraint);

        if ($this->isOwned()) {
            $this->getOwner()->add($constraint);
        }

        $fields = $constraint->getFields();

        // The first field adds the new constraint to others.
        if ($this->getField() === \array_shift($fields)) {
            // Add all relations to other fields.
            foreach ($fields as $field) {
                $field->getConstraintHandler()->add($constraint);
            }
        }

        return $this;
    }

    /**
     * Define the name.
     *
     * @param string $name
     * @return void
     */
    protected function setName(string $name)
    {
        $fieldname = $this->getField()->getName();

        if ($fieldname !== $name) {
            throw new \LogicException("The field field `{$fieldname}` is not the same as `$name`");
        }

        $this->name = $name;
    }

    /**
     * Add all constraints during ownership.
     *
     * @return void
     */
    protected function owned()
    {
        foreach ($this->all() as $constraints) {
            foreach ($constraints as $constraint) {
                $this->getOwner()->add($constraint);
            }
        }
    }

    /**
     * Create a constraint and add it.
     *
     * @param  string|mixed       $type
     * @param  string             $name
     * @param  Field|array<Field> $fields
     * @param  integer            $priority
     * @param  string             $class
     * @return BaseConstraint
     */
    public function create($type, string $name=null, $fields=[], int $priority=BaseConstraint::MEDIUM_PRIORITY,
                           string $class=null)
    {
        $fields = \is_array($fields) ? [$this->getField(), ...$fields] : [$this->getField(), $fields];
        $class = $class ?: $this->getConfig('classes.'.$type);

        $this->add($constraint = $class::constraint($fields, $name, $priority));

        return $constraint;
    }

    /**
     * Return the sourced constraint.
     *
     * @param array $attributes
     * @return RelationalConstraint
     */
    public function getSource(array $attributes=[]): RelationalConstraint
    {
        $sourceFields = [$this->getField(), ...$attributes];

        /** @var RelationalConstraint */
        foreach ($this->getConstraints() as $sourceable) {
            dump($sourceable);
            if (!($sourceable instanceof RelationalConstraint)) {
                continue;
            }

            /** @var RelationalConstraint $sourceable */
            $intersec = \array_diff(
                $sourceable->getAttributes(),
                $sourceFields
            );

            if (\count($intersec) === 0) {
                return $sourceable;
            }
        }

        throw new \Exception('No source found. A field used as a source must have a primary, unique or index constraint');
    }

    /**
     * Return the targeted constraint.
     *
     * @param array $attributes
     * @return Constraint
     */
    public function getTarget(array $attributes=[]): Constraint
    {
        $targetFields = [$this->getField(), ...$attributes];

        /** @var Constraint */
        foreach ($this->getConstraints() as $targetable) {
            if ($targetable instanceof RelationalConstraint) {
                continue;
            }

            /** @var Constraint $targetable */
            $intersec = \array_diff(
                $targetable->getAttributes(),
                $targetFields
            );

            if (\count($intersec) === 0) {
                return $targetable;
            }
        }

        throw new \Exception('No target found. A field used as a target must have a primary, unique or index constraint');
    }
}
