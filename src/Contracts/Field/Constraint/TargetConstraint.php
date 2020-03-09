<?php
/**
 * Define a targetable constraint contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field\Constraint;

use Laramore\Contracts\{
    Eloquent\LaramoreModel, Locked, Field\AttributeField
};

interface TargetConstraint extends Locked
{
    /**
     * Return the constraint name.
     *
     * @return string
     */
    public function getConstraintType(): string;

    /**
     * Return the main attirbute
     *
     * @return AttributeField
     */
    public function getMainAttribute(): AttributeField;

    /**
     * Return all concerned attribute fields.
     *
     * @return array
     */
    public function getAttributes(): array;

    /**
     * Indicate if this constraint is composed of multiple fields.
     *
     * @return boolean
     */
    public function isComposed(): bool;

    /**
     * Return values from constraint attributes.
     *
     * @param LaramoreModel $model
     * @return array
     */
    public function getModelValues(LaramoreModel $model): array;

    /**
     * Set values from constraint attributes.
     *
     * @param LaramoreModel $model
     * @param array         $values
     * @return array
     */
    public function setModelValues(LaramoreModel $model, array $values);

    /**
     * Reset values from constraint attributes.
     *
     * @param LaramoreModel $model
     * @return array
     */
    public function resetModelValues(LaramoreModel $model);

    /**
     * Return value from constraint main attribute.
     *
     * @param LaramoreModel $model
     * @return mixed
     */
    public function getModelValue(LaramoreModel $model);

    /**
     * Set value from constraint main attribute.
     *
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return mixed
     */
    public function setModelValue(LaramoreModel $model, $value);

    /**
     * Reset value from constraint main attribute.
     *
     * @param LaramoreModel $model
     * @return mixed
     */
    public function resetModelValue(LaramoreModel $model);
}
