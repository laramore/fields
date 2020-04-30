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

use Laramore\Contracts\Field\{
    AttributeField, Constraint\Constraint
};

class Primary extends BaseConstraint implements Constraint
{
    /**
     * Define the name of the constraint.
     *
     * @var string
     */
    protected $constraintType = self::PRIMARY;

    /**
     * Return the first field of this constraint.
     *
     * @return AttributeField
     */
    public function getAttribute(): AttributeField
    {
        if ($this->isComposed()) {
            throw new \LogicException("The `{$this->constraintType}` constraint `{$this->getName()}` is composed");
        }

        return $this->all()[0];
    }

    /**
     * Return the first attribute name.
     *
     * @return string
     */
    public function getAttname(): string
    {
        return $this->getAttribute()->attname;
    }

    /**
     * Return all attribute names.
     *
     * @return array<AttributeField>
     */
    public function getAttnames(): array
    {
        return \array_map(function ($field) {
            return $field->attname;
        }, $this->all());
    }
}
