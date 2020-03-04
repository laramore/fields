<?php
/**
 * Define an composed field contract.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Contracts\Field\Constraint;

use Laramore\Contracts\Field\Field;
use Laramore\Fields\Constraint\FieldConstraintHandler;

interface ConstraintedField extends Field
{
    /**
     * Return the relation handler for this meta.
     *
     * @return FieldConstraintHandler
     */
    public function getConstraintHandler(): FieldConstraintHandler;

    /**
     * Define a primary constraint.
     *
     * @param  string $name
     * @return self
     */
    public function primary(string $name=null);

    /**
     * Define a index constraint.
     *
     * @param  string $name
     * @return self
     */
    public function index(string $name=null);

    /**
     * Define a unique constraint.
     *
     * @param  string $name
     * @return self
     */
    public function unique(string $name=null);

    /**
     * Define a foreign constraint.
     *
     * @param  ConstraintedField $constrainedField
     * @param  string            $name
     * @return self
     */
    public function foreign(ConstraintedField $constrainedField, string $name=null);
}
