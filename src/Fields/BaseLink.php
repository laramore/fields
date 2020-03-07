<?php
/**
 * Define a link field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Laramore\Contracts\{
    Field\LinkField, Eloquent\LaramoreModel
};
use Laramore\Fields\BaseField;

abstract class BaseLink extends BaseField implements LinkField
{
    /**
     * Set the owner.
     *
     * @param mixed $owner
     * @return void
     */
    protected function setOwner($owner)
    {
        if (\is_null($this->off)) {
            throw new \Exception('You need to specify `off`');
        }

        $this->setMeta($this->off::getMeta());

        parent::setOwner($owner);
    }

    /**
     * Get the value definied by the field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function get(LaramoreModel $model)
    {
        $model->getRelationValue($this->getNative());
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
        $model->setRelationValue($this->getNative(), $value);
    }

    /**
     * Reet the value for the field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function reset(LaramoreModel $model)
    {
        return $model->setRelation($this->getNative(), $this->getProperty('default'));
    }
}
