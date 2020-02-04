<?php
/**
 * Define a boolean field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\Str;
use Laramore\Elements\{
    EnumElement, EnumManager
};
use Laramore\Exceptions\LockException;

class Enum extends AttributeField
{
    protected $elements;

    protected function setProxies()
    {
        parent::setProxies();

        $conf = config('field.proxies');
        $class = config('field.proxies.class');
        $data = \array_merge($conf['configurations'], $this->getConfig('elements.proxy'));

        if ($conf['enabled'] && $data['enabled']) {
            $proxyHandler = $this->getMeta()->getProxyHandler();

            foreach ($this->getElements()->all() as $value) {
                $name = static::replaceInTemplate($this->getConfig('elements.proxy.name_template'), [
                    'methodname' => 'is',
                    'elementname' => Str::camel((string) $value),
                    'fieldname' => Str::camel($this->name),
                ]);

                $proxyHandler->add($proxy = new $class($name, $this, 'is', $data['requirements'], $data['targets']));
                $proxy->setCallback(function ($element) use ($value) {
                    return $this->is($value, $element);
                });
            }
        }
    }

    public function elements($elements)
    {
        $this->checkNeedsToBeLocked(false);

        if ($elements instanceof EnumManager) {
            $this->defineProperty('elements', $elements);
        } else if (\is_array($elements)) {
            $this->defineProperty('elements', new EnumManager($elements));
        }

        return $this;
    }

    public function getElements(): EnumManager
    {
        return $this->elements;
    }

    public function getElementsValue()
    {
        return \array_keys($this->elements->all());
    }

    public function getElement($key): EnumElement
    {
        return $this->elements->get($key);
    }

    public function findElement($key): EnumElement
    {
        return $this->elements->find($key);
    }

    public function hasElement($key): bool
    {
        return $this->elements->has($key);
    }

    public function default($value=null)
    {
        return parent::default($this->getElement($value));
    }

    public function getDefaultValue()
    {
        return $this->default->name;
    }

    protected function locking()
    {
        parent::locking();

        $this->elements->lock();
    }

    /**
     * Check all properties and rules before locking the field.
     *
     * @return void
     */
    protected function checkRules()
    {
        if (!$this->hasProperty('elements') || $this->elements->count() === 0) {
            throw new LockException("Need a list of elements for {$this->getName()}", 'elements');
        }
    }

    public function cast($value)
    {
        return $this->transform($value);
    }

    public function dry($value)
    {
        return $this->transform($value)->native;
    }

    public function transform($value)
    {
        if (is_null($value) || ($value instanceof EnumElement)) {
            return $value;
        }

        return $this->getElement($value);
    }

    public function serialize($value)
    {
        return $value->native;
    }

    /**
     * Return if the value is the right element as expected or not.
     *
     * @param  EnumElement $value
     * @param  mixed       $element
     * @param  boolean     $expected
     * @return boolean
     */
    public function is(EnumElement $value, $element, bool $expected=true): bool
    {
        return ($value === $this->transform($element)) === $expected;
    }

    /**
     * Return if the value is not the right element.
     *
     * @param  EnumElement $value
     * @param  mixed       $element
     * @return boolean
     */
    public function isNot(EnumElement $value, $element): bool
    {
        return $this->is($value, $element, false);
    }
}
