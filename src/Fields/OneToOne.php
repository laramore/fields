<?php
/**
 * Define a one to one field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Laramore\Traits\Field\OneToRelation;

class OneToOne extends BaseComposed
{
    use OneToRelation;

    /**
     * Define the target.
     * 
     * @return void
     */
    protected function locking() 
    {
        parent::locking();

        $this->setTarget();
    }
}
