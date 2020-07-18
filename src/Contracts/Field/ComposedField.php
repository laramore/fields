<?php
/**
 * Define an composed field contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field;

use Illuminate\Support\Arr;

interface ComposedField extends ExtraField, FieldsOwner
{
    /**
     * Return the field value contained in model or array.
     *
     * @param string                                           $name
     * @param \Laramore\Contracts\Eloquent\LaramoreModel|array $model
     * @return mixed
     */
    public function getValue(string $name, $model);
}
