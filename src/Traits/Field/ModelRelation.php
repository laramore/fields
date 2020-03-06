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

use Laramore\Contracts\{
    Eloquent\LaramoreModel, Field\Field
};

trait ModelRelation
{
    /**
     * Get the value definied by the field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function get(LaramoreModel $model)
    {
        return $model->getRelationValue($this->getNative());
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
        return $model->setRelationValue($this->getNative(), $value);
    }

    /**
     * Reet the value for the field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function reset(LaramoreModel $model)
    {
        return $model->setRelationValue($this->getNative(), $this->getDefault());
    }

    /**
     * Return the set value for a specific field.
     *
     * @param Field         $field
     * @param LaramoreModel $model
     * @param mixed         $value
     * @return mixed
     */
    public function setFieldValue(Field $field, LaramoreModel $model, $value)
    {
        $this->reset($model);

        return parent::setFieldValue($field, $model, $value);
    }
}
