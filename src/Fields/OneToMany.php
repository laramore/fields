<?php
/**
 * Define a OneToMany field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Laramore\Traits\Field\OneToRelation;
use Laramore\Contracts\Eloquent\LaramoreModel;
use Laramore\Contracts\Field\{
    RelationField, Constraint\ConstraintedField
};

class OneToMany extends BaseComposed implements RelationField, ConstraintedField
{
    use OneToRelation {
        OneToRelation::set as protected setRelation;
        OneToRelation::reset as protected resetRelation;
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
        if (!\is_null($value)) {
            $this->getField('id')->set($model, $value[$this->to]);
        }

        $this->setRelation($model, $value);

        return $value;
    }

    /**
     * Reet the value for the field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function reset(LaramoreModel $model)
    {
        $this->getField('id')->reset($model);

        return $this->resetRelation($model);
    }

    /**
     * Return the query with this field as condition.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function relate(LaramoreModel $model)
    {
        return $model->belongsTo($this->on, $this->from, $this->to);
    }

    /**
     * Reverbate the relation into database.
     *
     * @param  LaramoreModel $model
     * @param  mixed         $value
     * @return boolean
     */
    public function reverbate(LaramoreModel $model, $value): bool
    {
        return $value->save();
    }
}
