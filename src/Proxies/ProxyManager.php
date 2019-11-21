<?php
/**
 * Handle all observers for a specific model.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Proxies;

use Laramore\Interfaces\{
    IsALaramoreManager, IsALaramoreModel
};
use Laramore\Observers\BaseManager;

class ProxyManager extends BaseManager implements IsALaramoreManager
{
    /**
     * Allowed observable sub class.
     *
     * @var string
     */
    protected $managedClass = IsALaramoreModel::class;

    /**
     * The observable handler class to generate.
     *
     * @var string
     */
    protected $handlerClass = ProxyHandler::class;
}
