<?php
/**
 * Groupe multiple proxies and use one of them based on the first argument.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Proxies;

use Laramore\Fields\BaseField;
use Laramore\Traits\IsLocked;
use Closure;

class MultiProxy extends BaseProxy
{
    /**
     * List of all proxies that this multi proxy can lead.
     *
     * @var array
     */
    protected $proxies = [];

    /**
     * An observer needs at least a name and a Closure.
     *
     * @param string  $name
     * @param integer $priority
     * @param array   $data
     */
    public function __construct(string $name, array $data=[])
    {
        parent::__construct($name, $name, $data);
    }

    /**
     * Return the field method name that is used for this proxy.
     *
     * @return string
     */
    public function getMethodName(): string
    {
        return $this->getName();
    }

    public function addProxy(Proxy $proxy)
    {
        $this->proxies[$proxy->getField()->getName()] = $proxy;

        $this->observed = \array_unique(\array_merge($this->all(), $proxy->all()));

        return $this;
    }

    public function hasProxy(string $fieldname)
    {
        return isset($this->proxies[$fieldname]);
    }

    public function getProxy(string $fieldname)
    {
        return $this->proxies[$fieldname];
    }

    public function getProxies()
    {
        return $this->proxies;
    }

    /**
     * Actions during locking.
     *
     * @return void
     */
    protected function locking()
    {
        $this->setCallback(function (string $fieldName, ...$args) {
            return $this->getProxy($fieldName)->getCallback()(...$args);
        });

        parent::locking();
    }
}
