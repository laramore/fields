<?php
/**
 * Extra field contract.
 * Usefull field to interact with models but corresponds
 * to no attribute field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field;

use Laramore\Contracts\Eloquent\LaramoreModel;

interface ExtraField extends Field
{
    /**
     * Retrieve values from the relation field.
     *
     * @param  LaramoreModel $model
     * @return mixed
     */
    public function retrieve(LaramoreModel $model);
}