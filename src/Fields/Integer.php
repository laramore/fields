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
    Option, Type
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
        if ($this->hasOption(Option::unsigned())) {
            return Type::get($this->getConfig('unsigned_type'));
        }

        return $this->resolveType();
    }

    /**
     * Force the value to be unsigned or not, positive or not.
     *
     * @param boolean $unsigned
     * @param boolean $positive
     *
     * @return self
     */
    public function unsigned(bool $unsigned=true, bool $positive=true)
    {
        $this->needsToBeUnlocked();

        if ($unsigned) {
            if ($positive) {
                return $this->positive();
            }

            return $this->negative();
        }

        $this->removeOption(Option::negative());
        $this->removeOption(Option::unsigned());

        return $this;
    }

    /**
     * Force the value to be positive.
     *
     * @return self
     */
    public function positive()
    {
        $this->needsToBeUnlocked();

        $this->addOption(Option::unsigned());
        $this->removeOption(Option::negative());

        return $this;
    }

    /**
     * Force the value to be negative.
     *
     * @return self
     */
    public function negative()
    {
        $this->needsToBeUnlocked();

        $this->addOption(Option::negative());

        return $this;
    }

    /**
     * Dry the value in a simple format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function dry($value)
    {
        return is_null($value) ? $value : (int) $value;
    }

    /**
     * Cast the value in the correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast($value)
    {
        return $this->transform($this->dry($value));
    }

    /**
     * Transform the value to be used as a correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function transform($value)
    {
        if (is_null($value)) {
            return $value;
        }

        if ($this->hasOption(Option::unsigned())) {
            $newValue = abs($value);

            if ($this->hasOption(Option::negative())) {
                $newValue = (- $newValue);
            }

            $value = $newValue;
        }

        return $value;
    }

    /**
     * Serialize the value for outputs.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function serialize($value)
    {
        return $value;
    }
}
