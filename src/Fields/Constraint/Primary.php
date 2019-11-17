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

class Primary extends Constraint
{
    /**
     * Define the name of the constraint.
     *
     * @var string
     */
    protected $constraintName = 'primary';

    /**
     * Indicate if the primary key is a composed key.
     *
     * @return boolean
     */
    public function isComposed(): bool
    {
        return $this->count() > 1;
    }

    /**
     * Return the first attribute name.
     *
     * @return string
     */
    public function getAttribute(): string
    {
        return $this->all()[0]->attname;
    }

    /**
     * Return all attribute names.
     *
     * @return array
     */
    public function getAttributes(): array
    {
        return \array_map(function ($field) {
            return $field->attname;
        }, $this->all());
    }
}
