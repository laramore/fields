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
use Laramore\Fields\AttributeField;

class Foreign extends Constraint
{
    /**
     * Define the name of the constraint.
     *
     * @var string
     */
    protected $constraintName = 'foreign';

    /**
     * Actions during locking.
     *
     * @return void
     */
    protected function locking()
    {
        if ($this->count() !== 2) {
            throw new LockException('You must define two fields for a foreign constraint', 'fields');
        }
    }

    /**
     * Return the attribute that points to another.
     *
     * @return AttributeField
     */
    public function getOffField(): AttributeField
    {
        return $this->all()[0];
    }

    /**
     * Return the attribute that is pointed by this foreign relation.
     *
     * @return AttributeField
     */
    public function getOnField(): AttributeField
    {
        return $this->all()[1];
    }
}
