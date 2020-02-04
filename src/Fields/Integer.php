<?php
/**
 * Define a number field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Laramore\Elements\TypeElement;
use Laramore\Facades\{
    Rule, Type
};

class Integer extends AttributeField
{
    /**
     * Return the type object of the field.
     *
     * @return Type
     */
    public function getType(): TypeElement
    {
        if ($this->hasRule(Rule::unsigned())) {
            return Type::get($this->getConfig('unsigned_type'));
        }

        return $this->resolveType();
    }

    public function unsigned(bool $unsigned=true, bool $positive=true)
    {
        $this->needsToBeUnlocked();

        if ($unsigned) {
            if ($positive) {
                return $this->positive();
            }

            return $this->negative();
        }

        $this->removeRule(Rule::negative());
        $this->removeRule(Rule::unsigned());

        return $this;
    }

    public function positive()
    {
        $this->needsToBeUnlocked();

        $this->addRule(Rule::unsigned());
        $this->removeRule(Rule::negative());

        return $this;
    }

    public function negative()
    {
        $this->needsToBeUnlocked();

        $this->addRule(Rule::negative());

        return $this;
    }

    public function dry($value)
    {
        return is_null($value) ? $value : (int) $value;
    }

    public function cast($value)
    {
        return $this->transform($this->dry($value));
    }

    public function transform($value)
    {
        if (is_null($value)) {
            return $value;
        }

        if ($this->hasRule(Rule::unsigned())) {
            $newValue = abs($value);

            if ($this->hasRule(Rule::negative())) {
                $newValue = - $newValue;
            }

            $value = $newValue;
        }

        return $value;
    }

    public function serialize($value)
    {
        return $value;
    }
}
