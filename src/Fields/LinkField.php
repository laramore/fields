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

use Laramore\Interfaces\{
    IsAnOwner, IsARelationField, IsALaramoreModel
};
use Laramore\Fields\BaseField;

abstract class LinkField extends BaseField implements IsARelationField
{
    /**
     * Set the owner.
     *
     * @param IsAnOwner $owner
     * @return void
     */
    protected function setOwner(IsAnOwner $owner)
    {
        if (\is_null($this->off)) {
            throw new \Exception('You need to specify `off`');
        }

        $this->setMeta($this->off::getMeta());

        parent::setOwner($owner);
    }

    /**
     * Callaback when the instance is owned.
     *
     * @return void
     */
    protected function owned()
    {
        parent::owned();

        $this->getMeta()->setLink($this->name, $this);
    }

    /**
     * Get the value definied by the field.
     *
     * @param  IsALaramoreModel $model
     * @return mixed
     */
    public function get(IsALaramoreModel $model)
    {
        $model->getRelationValue($this->getNative());
    }

    /**
     * Set the value for the field.
     *
     * @param  IsALaramoreModel $model
     * @param  mixed            $value
     * @return mixed
     */
    public function set(IsALaramoreModel $model, $value)
    {
        $model->setRawRelationValue($this->getNative, $value);
    }

    /**
     * Reet the value for the field.
     *
     * @param  IsALaramoreModel $model
     * @return mixed
     */
    public function reset(IsALaramoreModel $model)
    {
        return $model->setRawRelation($this->getNative(), $this->getProperty('default'));
    }
}
