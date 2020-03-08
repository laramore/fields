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

use Laramore\Contracts\Field\Constraint\TargetConstraint;
use Laramore\Traits\Field\Constraint\ManipulateConstraintedFields;

class Index extends BaseConstraint implements TargetConstraint
{
    use ManipulateConstraintedFields;

    /**
     * Define the name of the constraint.
     *
     * @var string
     */
    protected $constraintType = self::INDEX;
}
