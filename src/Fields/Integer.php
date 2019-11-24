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

use Illuminate\Database\Eloquent\Model;
use Laramore\Validations\NotZero;
use Laramore\Elements\Type;
use Rules, Types;

class Integer extends Field
{
    /**
     * Return the type object of the field.
     *
     * @return Type
     */
    public function getType(): Type
    {
        if ($this->hasRule(Rules::unsigned())) {
            return Types::get($this->getConfig('unsigned_type'));
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

        $this->removeRule(Rules::negative());
        $this->removeRule(Rules::unsigned());

        return $this;
    }

    public function positive()
    {
        $this->needsToBeUnlocked();

        $this->addRule(Rules::unsigned());
        $this->removeRule(Rules::negative());

        return $this;
    }

    public function negative()
    {
        $this->needsToBeUnlocked();

        $this->addRule(Rules::negative());

        return $this;
    }

    protected function setValidations()
    {
        parent::setValidations();

        if ($this->hasRule(Rules::notZero())) {
            $this->setValidation(NotZero::class);
        }
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

        if ($this->hasRule(Rules::unsigned())) {
            $newValue = abs($value);

            if ($this->hasRule(Rules::negative())) {
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
