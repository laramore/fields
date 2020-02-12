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
     * Retrieve values from the relation field.
     *
     * @param  IsALaramoreModel $model
     * @return mixed
     */
    public function retrieve(IsALaramoreModel $model);

    /**
     * Use the relation to set the other field values.
     *
     * @param  IsALaramoreModel $model
     * @param  mixed            $value
     * @return mixed
     */
    public function consume(IsALaramoreModel $model, $value);

    /**
     * Return the query with this field as condition.
     *
     * @param  IsALaramoreModel $model
     * @return Builder
     */
    public function relate(IsALaramoreModel $model);

    /**
     * Reverbate the relation into database.
     *
     * @param  IsALaramoreModel $model
     * @param  mixed            $value
     * @return boolean
     */
    public function reverbate(IsALaramoreModel $model, $value): bool;
}
