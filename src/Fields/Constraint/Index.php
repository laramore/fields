<?php
/**
 * Define a index constraint.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields\Constraint;

use Laramore\Contracts\Field\Constraint\Constraint;

class Index extends BaseConstraint implements Constraint
{
    /**
     * Define the name of the constraint.
     *
     * @var string
     */
    protected $constraintType = self::INDEX;
}
