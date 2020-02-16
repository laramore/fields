<?php
/**
 * Handle all observers for a specific model.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields\Proxy;

use Laramore\Observers\{
    BaseHandler, BaseObserver
};

class ProxyHandler extends BaseHandler
{
    /**
     * The observable class.
     *
     * @var string
     */
    protected $observerClass = BaseProxy::class;

    public const MODEL_TYPE = 'model';
    public const BUILDER_TYPE = 'builder';

    /**
     * Add an observer to a list of observers.
     *
     * @param BaseObserver        $proxy
     * @param array<BaseObserver> $proxys
     * @return self
     */
    protected function push(BaseObserver $proxy, array &$proxys)
    {
        \array_push($proxys, $proxy);

        if ($this->has($name = $proxy->getMultiProxyName())) {
            $multiProxy = $this->get($name);

            if (!($multiProxy instanceof MultiProxy)) {
                throw new \LogicException("Conflict between field method names [$name]");
            }
        } else {
            \array_push($proxys, $multiProxy = new MultiProxy($name));
        }

        $multiProxy->addProxy($proxy);

        return $this;
    }

    /**
     * Return if an observe exists with the given name.
     *
     * @param  string $name
     * @param  string $instanceType
     * @return boolean
     */
    public function has(string $name, string $instanceType=null): bool
    {
        foreach ($this->observers as $key => $proxy) {
            if ($proxy->getName() === $name) {
                return is_null($instanceType) || $proxy->has($instanceType);
            }
        }

        return false;
    }

    /**
     * Return the first observer with the given name.
     *
     * @param  string $name
     * @param  string $instanceType
     * @return BaseObserver
     */
    public function get(string $name, string $instanceType=null)
    {
        $proxy = parent::get($name);

        if (!\is_null($instanceType) && !$proxy->has($instanceType)) {
            throw new \Exception("The proxy [$name] does not manage the instance type [$instanceType]");
        }

        return $proxy;
    }
}
