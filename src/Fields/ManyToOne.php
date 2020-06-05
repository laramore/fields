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

use Laramore\Contracts\{
    Field\RelationField, Eloquent\LaramoreModel
};
use Laramore\Traits\Field\ToSingleOneRelation;

class ManyToOne extends BaseComposed implements RelationField
{
    use ToSingleOneRelation {
        ToSingleOneRelation::reset as protected resetRelation;
    }

    /**
     * On update action.
     *
     * @var string
     */
    protected $onUpdate;

    /**
     * On delete action.
     *
     * @var string
     */
    protected $onDelete;

    public const CASCADE = 'cascade';
    public const RESTRICT = 'restrict';
    public const SET_NULL = 'set null';
    public const SET_DEFAULT = 'set default';

    /**
     * Reet the value for the field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function reset(LaramoreModel $model)
    {
        $this->resetRelation($model);

        $this->resetFieldValue($this->getField('id'), $model);
    }
}
