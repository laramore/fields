<?php
/**
 * Define a foreign field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Laramore\Eloquent\Builder;
use Laramore\Traits\Field\OneToOneRelation;
use Laramore\Interfaces\{
    IsALaramoreModel, IsProxied
};

class Foreign extends CompositeField
{
    use OneToOneRelation;

    /**
     * Use the relation to set the other field values.
     *
     * @param  IsALaramoreModel $model
     * @param  mixed            $value
     * @return mixed
     */
    public function consume(IsALaramoreModel $model, $value)
    {
        $model->setAttribute($this->getAttribute('id')->attname, $value[$this->to]);

        return $value;
    }

    /**
     * Return the query with this field as condition.
     *
     * @param  IsALaramoreModel $model
     * @return Builder
     */
    public function relate(IsALaramoreModel $model)
    {
        return $model->belongsTo($this->on, $this->from, $this->to);
    }

    /**
     * Reverbate the relation into database.
     *
     * @param  IsALaramoreModel $model
     * @param  mixed            $value
     * @return boolean
     */
    public function reverbate(IsALaramoreModel $model, $value): bool
    {
        return $value->save();
    }
}
