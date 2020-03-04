<?php
/**
 * Relation field contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field;

use Laramore\Contracts\Eloquent\LaramoreModel;

interface RelationField extends ExtraField
{
    /**
     * Return the relation with this field.
     *
     * @param  LaramoreModel $instance
     * @return Builder
     */
    public function relate(LaramoreModel $instance);

    /**
     * Reverbate the relation into database.
     *
     * @param  LaramoreModel $model
     * @param  mixed         $value
     * @return boolean
     */
    public function reverbate(LaramoreModel $model, $value): bool;
}
