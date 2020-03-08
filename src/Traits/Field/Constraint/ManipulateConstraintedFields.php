<?php
/**
 * Add management for field constraints.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Traits\Field\Constraint;

use Illuminate\Support\Arr;
use Laramore\Contracts\Eloquent\LaramoreModel;

trait ManipulateConstraintedFields
{
    /**
     * Return values from constraint attributes.
     *
     * @param LaramoreModel $model
     * @return array
     */
    public function getModelValues(LaramoreModel $model): array
    {
        $values = [];

        foreach ($this->getAttributes() as $attribute) {
            $values[$attribute->getNative()] = $attribute->getOwner()->getFieldValue($attribute, $model);
        }

        return $values;
    }

    /**
     * Set values from constraint attributes.
     *
     * @param LaramoreModel $model
     * @param array $values
     * @return array
     */
    public function setModelValues(LaramoreModel $model, array $values)
    {
        foreach (\array_values($this->getAttributes()) as $index => $attribute) {
            $value = Arr::isAssoc($values) ? $values[$attribute->getNative()] : $values[$index];

            $attribute->getOwner()->setFieldValue($attribute, $model, $value);
        }
    }

    /**
     * Reset values from constraint attributes.
     *
     * @param LaramoreModel $model
     * @return array
     */
    public function resetModelValues(LaramoreModel $model)
    {
        foreach (\array_values($this->getAttributes()) as $attribute) {
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
     * @param array $values
     * @return array
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
     * @return array
     */
    public function resetModelValue(LaramoreModel $model)
    {
        $attribute = $this->getMainAttribute();

        $attribute->getOwner()->resetFieldValue($attribute, $model);
    }
}
