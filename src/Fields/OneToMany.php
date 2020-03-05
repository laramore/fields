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
    use OneToRelation;

    /**
     * Use the relation to set the other field values.
     *
     * @param  LaramoreModel $model
     * @param  mixed         $value
     * @return mixed
     */
    public function consume(LaramoreModel $model, $value)
    {
        $model->setAttribute($this->getField('id')->attname, $value[$this->to]);

        return $value;
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
