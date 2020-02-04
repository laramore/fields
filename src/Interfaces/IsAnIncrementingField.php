<?php
/**
 * Define an incrementing field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Interfaces;

interface IsAnIncrementingField extends IsAField
{
    /**
     * Increment the attribute value by the desired number (1 by default).
     *
     * @param IsALaramoreModel $model
     * @param integer|float    $value
     * @param integer|float    $increment
     *
     * @return void
     */
    public function increment(IsALaramoreModel $model, $value, $increment=1);

    /**
     * Decrement the attribute value by the desired number (1 by default).
     *
     * @param IsALaramoreModel $model
     * @param integer|float    $value
     * @param integer|float    $decrement
     *
     * @return void
     */
    public function decrement(IsALaramoreModel $model, $value, $decrement=1);
}
