<?php
/**
 * Handle all constraints adding a primary constraints during creation.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Fields\Constraint;

use Laramore\Contracts\Field\Constraint\ConstraintedField;

class PrimaryConstraintHandler extends FieldConstraintHandler
{
    /**
     * Create a field handler for a specific field.
     *
     * @param ConstraintedField $constrainted
     */
    public function __construct(ConstraintedField $constrainted)
    {
        parent::__construct($constrainted);

        $this->createPrimary();
    }
}
