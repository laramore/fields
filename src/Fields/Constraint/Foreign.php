<?php
/**
 * Define a foreign constraint.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields\Constraint;

use Laramore\Exceptions\LockException;
use Laramore\Contracts\Field\AttributeField;

class Foreign extends BaseConstraint
{
    /**
     * Define the name of the constraint.
     *
     * @var string
     */
    protected $constraintType = self::FOREIGN;

    /**
     * Actions during locking.
     *
     * @return void
     */
    protected function locking()
    {
        if (($this->count() % 2) !== 0) {
            throw new LockException('You must define at least one source and target fields for a foreign constraint', 'fields');
        }
    }

    /**
     * Return the attributes that points to another.
     *
     * @return array<AttributeField>
     */
    public function getSourceAttributes(): array
    {
        $attributes = $this->all();

        return \array_slice($attributes, 0, (\count($attributes) / 2));
    }

    /**
     * Return the attributes that is pointed by this foreign relation.
     *
     * @return array<AttributeField>
     */
    public function getTargetAttributes(): array
    {
        $attributes = $this->all();

        return \array_slice($attributes, (\count($attributes) / 2));
    }

    /**
     * Return the attribute that points to another.
     *
     * @return AttributeField
     */
    public function getSourceAttribute(): AttributeField
    {
        return $this->all()[0];
    }

    /**
     * Return the attribute that is pointed by this foreign relation.
     *
     * @return AttributeField
     */
    public function getTargetAttribute(): AttributeField
    {
        $attributes = $this->all();

        return $attributes[(\count($attributes) / 2)];
    }
}
