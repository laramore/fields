<?php
/**
 * Create an Observer to add a \Closure on a specific model event.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Proxies;

use Laramore\Fields\BaseField;
use Laramore\Traits\IsLocked;
use Laramore\Observers\BaseObserver;
use Closure;

abstract class BaseProxy extends BaseObserver
{
    protected $methodName;
    protected $injections;

    /**
     * An observer needs at least a name and a Closure.
     *
     * @param string  $name
     * @param integer $priority
     * @param array   $data
     */
    public function __construct(string $name, string $methodName, array $injections=[], array $data=[])
    {
        $this->setMethodName($methodName);
        $this->setInjections($injections);

        parent::__construct($name, null, self::MEDIUM_PRIORITY, $data);
    }

    /**
     * Define the field method name that is used for this proxy.
     *
     * @param string $methodName
     * @return self
     */
    public function setMethodName(string $methodName)
    {
        $this->needsToBeUnlocked();

        $this->methodName = $methodName;

        return $this;
    }

    /**
     * Return the field method name that is used for this proxy.
     *
     * @return string
     */
    public function getMethodName(): string
    {
        return $this->methodName;
    }

    /**
     * Define the arguments to inject.
     *
     * @param array $injections
     * @return self
     */
    public function setInjections(array $injections)
    {
        $this->needsToBeUnlocked();

        $this->injections = $injections;

        return $this;
    }

    /**
     * Return the arguments to inject.
     *
     * @return array
     */
    public function getInjections(): array
    {
        return $this->injections;
    }
}
