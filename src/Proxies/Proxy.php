<?php
/**
 * A proxy defines the field to use with which method to call.
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

class Proxy extends BaseProxy
{
    /**
     * The field to use for the call.
     * 
     * @var BaseField
     */
    protected $field;

    /**
     * An observer needs at least a name and a Closure.
     *
     * @param string  $name
     * @param integer $priority
     * @param array   $data
     */
    public function __construct(string $name, BaseField $field, string $methodName, array $injections=[], array $data=[])
    {
        parent::__construct($name, $methodName, $injections, $data);

        $this->setField($field);
        $this->setCallback(Closure::fromCallable([$this, 'resolveCallback']));
    }

    /**
     * Define the proxy field.
     *
     * @param BaseField $field
     * @return self
     */
    public function setField(BaseField $field)
    {
        $this->needsToBeUnlocked();

        $this->field = $field;

        return $this;
    }

    /**
     * Return the proxy field.
     *
     * @return BaseField
     */
    public function getField(): BaseField
    {
        return $this->field;
    }

    /**
     * Resolve one time the callback and save it so it can be callable.
     *
     * @param mixed ...$args
     * @return mixed
     */
    public function resolveCallback(...$args)
    {
        $field = $this->getField();
        $owner = $field->getOwner();
        $methodName = $this->getMethodName();

        if (\method_exists($owner, $methodOwnerName = "${methodName}FieldAttribute")) {
            $this->callback = function (...$args) use ($owner, $field, $methodOwnerName) {
                return \call_user_func([$owner, $methodOwnerName], $field, ...$args);
            };
        } else {
            $this->callback = function (...$args) use ($owner, $field, $methodName) {
                return \call_user_func([$owner, 'callFieldAttributeMethod'], $field, $methodName, $args);
            };
        }

        return \call_user_func($this->getCallback(), ...$args);
    }
}