<?php
/**
 * Create an Observer to add a \Closure on a specific model event.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields\Proxy;

use Laramore\Observers\BaseObserver;

abstract class BaseProxy extends BaseObserver
{
    /**
     * The method to call.
     *
     * @var string
     */
    protected $methodName;

    /**
     * List of all variables to inject.
     *
     * @var array<string>
     */
    protected $injections;

    /**
     * An observer needs at least a name and a Closure.
     *
     * @param string        $name
     * @param string        $methodName
     * @param array<string> $injections
     * @param array         $data
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
     * @param array<string> $injections
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
     * @return array<string>
     */
    public function getInjections(): array
    {
        return $this->injections;
    }
}
