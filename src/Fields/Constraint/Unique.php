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

use Laramore\Contracts\Field\Constraint\TargetConstraint;
use Laramore\Traits\Field\Constraint\ManipulateConstraintedFields;

class Unique extends BaseConstraint implements TargetConstraint
{
    use ManipulateConstraintedFields;

    /**
     * Define the name of the constraint.
     *
     * @var string
     */
    protected $constraintType = self::UNIQUE;
}
