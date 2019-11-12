<?php
/**
 * Define a unique constraint.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields\Constraint;

class Unique extends Constraint
{
    /**
     * Define the name of the constraint.
     *
     * @var string
     */
    protected $constraintName = 'unique';
}
