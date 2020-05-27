<?php
/**
 * Add management for relation fields.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Traits\Field;

use Laramore\Contracts\Eloquent\LaramoreModel;

trait ModelRelation
{
    /**
     * Define condition on relation.
     *
     * @var callable|\Closure
     */
    protected $when;

    /**
     * Add a condition to the relation.
     *
     * @param  callable|\Closure $callable
     * @return self
     */
    public function when($callable)
    {
        $this->needsToBeUnLocked();

        $this->when = $callable;

        return $this;
    }

    /**
     * Indicate if the field has a value.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function has(LaramoreModel $model)
    {
        return $model->hasRelationValue($this->getName());
    }

    /**
     * Get the value definied by the field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function get(LaramoreModel $model)
    {
        return $model->getRelationValue($this->getName());
    }

    /**
     * Set the value for the field.
     *
     * @param  LaramoreModel $model
     * @param  mixed         $value
     * @return mixed
     */
    public function set(LaramoreModel $model, $value)
    {
        return $model->setRelationValue($this->getName(), $value);
    }

    /**
     * Reet the value for the field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function reset(LaramoreModel $model)
    {
        if ($this->hasDefault()) {
            $model->setRelationValue($this->getName(), $value = $this->getDefault());

            return $value;
        }

        $model->unsetRelation($this->getName());
    }

    /**
     * Retrieve values from the relation field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function retrieve(LaramoreModel $model)
    {
        return $this->relate($model)->getResults();
    }

    /**
     * Update a relation.
     *
     * @param LaramoreModel $model
     * @param array         $value
     * @return boolean
     */
    public function update(LaramoreModel $model, array $value): bool
    {
        return $this->relate($model)->update($value);
    }

    /**
     * Delete a relation.
     *
     * @param LaramoreModel $model
     * @return integer
     */
    public function delete(LaramoreModel $model): int
    {
        return $this->relate($model)->delete();
    }
}
