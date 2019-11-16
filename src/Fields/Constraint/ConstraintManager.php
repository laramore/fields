<?php
/**
 * Group all handlers in a manager.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields\Constraint;

use Illuminate\Database\Eloquent\Model;
use Laramore\Observers\BaseManager;
use Laramore\Interfaces\IsALaramoreManager;

class ConstraintManager extends BaseManager implements IsALaramoreManager
{
    /**
     * Allowed observable sub class.
     *
     * @var string
     */
    protected $managedClass = Model::class;

    /**
     * The observable handler class to generate.
     *
     * @var string
     */
    protected $handlerClass = ConstraintHandler::class;
}
