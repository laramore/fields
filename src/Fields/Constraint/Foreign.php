<?php
/**
 * Define a foreign constraint.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields\Constraint;

use Laramore\Exceptions\LockException;
use Laramore\Contracts\Eloquent\LaramoreModel;
use Laramore\Contracts\Field\{
    AttributeField, Constraint\SourceConstraint
};

class Foreign extends BaseConstraint implements SourceConstraint
{
    /**
     * Define the name of the constraint.
     *
     * @var string
     */
    protected $constraintType = self::FOREIGN;

    /**
     * Actions during locking.
     *
     * @return void
     */
    protected function locking()
    {
        if (($this->count() % 2) !== 0) {
            throw new LockException('You must define at least one source and target fields for a foreign constraint', 'fields');
        }
    }

    /**
     * Return the attributes that points to another.
     *
     * @return array<AttributeField>
     */
    public function getSourceAttributes(): array
    {
        $attributes = $this->all();

        return \array_slice($attributes, 0, (\count($attributes) / 2));
    }

    /**
     * Return the attributes that is pointed by this foreign relation.
     *
     * @return array<AttributeField>
     */
    public function getTargetAttributes(): array
    {
        $attributes = $this->all();

        return \array_slice($attributes, (\count($attributes) / 2));
    }

    /**
     * Return the attribute that points to another.
     *
     * @return AttributeField
     */
    public function getSourceAttribute(): AttributeField
    {
        return $this->all()[0];
    }

    /**
     * Return the attribute that is pointed by this foreign relation.
     *
     * @return AttributeField
     */
    public function getTargetAttribute(): AttributeField
    {
        $attributes = $this->all();

        return $attributes[(\count($attributes) / 2)];
    }

    /**
     * Return values from constraint attributes.
     *
     * @param LaramoreModel $model
     * @return array
     */
    public function getModelValues(LaramoreModel $model): array
    {
        $values = [];

        foreach ($this->getSourceAttributes() as $attribute) {
            $values[$attribute->getNative()] = $attribute->getOwner()->getFieldValue($attribute, $model);
        }

        return $values;
    }

    /**
     * Set values from constraint attributes.
     *
     * @param LaramoreModel $model
     * @param array         $values
     * @return void
     */
    public function setModelValues(LaramoreModel $model, array $values)
    {
        foreach (\array_values($this->getSourceAttributes()) as $index => $attribute) {
            $value = Arr::isAssoc($values) ? $values[$attribute->getNative()] : $values[$index];

            $attribute->getOwner()->setFieldValue($attribute, $model, $value);
        }
    }

    /**
     * Reset values from constraint attributes.
     *
     * @param LaramoreModel $model
     * @return void
     */
    public function resetModelValues(LaramoreModel $model)
    {
        foreach (\array_values($this->getSourceAttributes()) as $attribute) {
            $attribute->getOwner()->resetFieldValue($attribute, $model);
        }
    }

    /**
     * Return value from constraint main attribute.
     *
     * @param LaramoreModel $model
     * @return array
     */
    public function getModelValue(LaramoreModel $model)
    {
        $attribute = $this->getMainAttribute();

        return $attribute->getOwner()->getFieldValue($attribute, $model);
    }

    /**
     * Set value from constraint main attribute.
     *
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return void
     */
    public function setModelValue(LaramoreModel $model, $value)
    {
        $attribute = $this->getMainAttribute();

        $attribute->getOwner()->setFieldValue($attribute, $model, $value);
    }

    /**
     * Reset value from constraint main attribute.
     *
     * @param LaramoreModel $model
     * @return void
     */
    public function resetModelValue(LaramoreModel $model)
    {
        $attribute = $this->getMainAttribute();

        $attribute->getOwner()->resetFieldValue($attribute, $model);
    }
}
