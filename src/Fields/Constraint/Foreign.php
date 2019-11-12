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
use Laramore\Fields\Field;

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
            throw new LockException('You must define two fields for a foreign constraint');
        }
    }

    public function getOffField(): Field
    {
        return $this->all()[0];
    }

    public function getOnField(): Field
    {
        return $this->all()[1];
    }
}
