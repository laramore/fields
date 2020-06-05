<?php
/**
 * Define a primary constraint.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields\Constraint;

use Laramore\Contracts\Field\Constraint\Constraint;

class Primary extends BaseConstraint implements Constraint
{
    /**
     * Define the name of the constraint.
     *
     * @var string
     */
    protected $constraintType = self::PRIMARY;
}
