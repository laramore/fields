<?php
/**
 * Field interface.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Interfaces;

interface IsARelationField extends IsAField
{
    /**
     * Return the relation with this field.
     *
     * @param  IsALaramoreModel $instance
     * @return Builder
     */
    public function relate(IsALaramoreModel $instance);

    /**
     * Retrieve values from the relation field.
     *
     * @param  IsALaramoreModel $model
     * @return mixed
     */
    public function retrieve(IsALaramoreModel $model);

    /**
     * Reverbate the relation into database.
     *
     * @param  IsALaramoreModel $model
     * @param  mixed            $value
     * @return boolean
     */
    public function reverbate(IsALaramoreModel $model, $value): bool;
}
