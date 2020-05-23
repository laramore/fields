<?php
/**
 * Add management for extra fields.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Traits\Field;

use Laramore\Contracts\Eloquent\LaramoreModel;

trait ModelExtra
{
    /**
     * Get the value definied by the field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function get(LaramoreModel $model)
    {
        return $model->getExtraValue($this->getName());
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
        return $model->setExtraValue($this->getName(), $value);
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
            $model->setExtraValue($this->getName(), $value = $this->getDefault());

            return $value;
        }

        $model->unsetExtra($this->getName());
    }
}
