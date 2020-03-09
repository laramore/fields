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
    Owned, Configured, Field\Constraint\ConstraintedField
};
use Laramore\Exceptions\LockException;
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
    protected $observableClass = ConstraintedField::class;

    /**
     * The observer class to use to generate.
     *
     * @var string
     */
    protected $observerClass = BaseConstraint::class;

    /**
     * ConstraintedField field.
     *
     * @var ConstraintedField
     */
    protected $constrainted;

    /**
     * Create a field handler for a specific field.
     *
     * @param ConstraintedField $constrainted
     */
    public function __construct(ConstraintedField $constrainted)
    {
        $this->observableClass = \get_class($constrainted);
        $this->constrainted = $constrainted;
    }

    /**
     * Return the constrainted field.
     *
     * @return ConstraintedField
     */
    public function getConstrainted(): ConstraintedField
    {
        return $this->constrainted;
    }

    /**
     * Add an observer for a specific model event.
     *
     * @param BaseObserver $observer
     * @return self
     */
    public function add(BaseObserver $observer)
    {
        parent::add($observer);

        if ($this->isOwned()) {
            $this->getOwner()->add($observer);
        }

        $attributes = $observer->getAttributes();

        if ($this->getConstrainted() === \array_shift($attributes)) {
            // Add all relations to other fields.
            foreach ($attributes as $attribute) {
                $attribute->getConstraintHandler()->add($observer);
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
        $fieldname = $this->getConstrainted()->getName();

        if ($fieldname !== $name) {
            throw new \LogicException("The constrainted field `{$fieldname}` is not the same as `$name`");
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
     * Create a primary constraint and add it.
     *
     * @param  string                                     $name
     * @param  ConstraintedField|array<ConstraintedField> $fields
     * @return self
     */
    public function createPrimary(string $name=null, $fields=[])
    {
        if ($this->count(BaseConstraint::PRIMARY)) {
            throw new \LogicException('Cannot have multiple primary constraints.');
        }

        $fields = is_array($fields) ? [$this->getConstrainted(), ...$fields] : [$this->getConstrainted(), $fields];
        $class = $this->getConfig('classes.'.BaseConstraint::PRIMARY);

        return $this->add($class::constraint($fields, $name));
    }

    /**
     * Create a index constraint and add it.
     *
     * @param  string                                     $name
     * @param  ConstraintedField|array<ConstraintedField> $fields
     * @return self
     */
    public function createIndex(string $name=null, $fields=[])
    {
        $fields = is_array($fields) ? [$this->getConstrainted(), ...$fields] : [$this->getConstrainted(), $fields];
        $class = $this->getConfig('classes.'.BaseConstraint::INDEX);

        return $this->add($class::constraint($fields, $name));
    }

    /**
     * Create a unique constraint and add it.
     *
     * @param  string                                     $name
     * @param  ConstraintedField|array<ConstraintedField> $fields
     * @return self
     */
    public function createUnique(string $name=null, $fields=[])
    {
        $fields = is_array($fields) ? [$this->getConstrainted(), ...$fields] : [$this->getConstrainted(), $fields];
        $class = $this->getConfig('classes.'.BaseConstraint::UNIQUE);

        return $this->add($class::constraint($fields, $name));
    }

    /**
     * Create a foreign constraint and add it.
     *
     * @param  ConstraintedField $field
     * @param  string            $name
     * @return self
     */
    public function createForeign(ConstraintedField $field, string $name=null)
    {
        if ($this->count(BaseConstraint::FOREIGN)) {
            throw new \LogicException('Cannot have multiple primary constraints.');
        }

        $class = $this->getConfig('classes.'.BaseConstraint::FOREIGN);

        return $this->add($class::constraint([$this->getConstrainted(), $field], $name));
    }

    /**
     * Return the targeted constraint.
     *
     * @param array $attributes
     * @return TargetConstraint
     */
    public function getTarget(array $attributes=[])
    {
        foreach ($this->getTargets() as $targetable) {
            $intersec = \array_diff(
                $targetable->getNatives(),
                \array_merge($attributes, [$this->getConstrainted()->getNative()])
            );

            if (\count($intersec) === 0) {
                return $targetable;
            }
        }

        throw new \Exception('No target found. A field used as a target must have a primary, unique or index constraint');
    }

    /**
     * Actions during locking.
     *
     * @return void
     */
    public function locking()
    {
        parent::locking();

        if ($this->count(BaseConstraint::PRIMARY) > 1) {
            throw new LockException('A field cannot have multiple primary constraints', 'primary');
        }
    }
}
