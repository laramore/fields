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

class Boolean extends AttributeField
{
    /**
     * Dry the value in a simple format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function dry($value)
    {
        return $this->transform($value);
    }

    /**
     * Cast the value in the correct format.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function cast($value)
    {
        return $this->transform($value);
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

        return (boolean) $value;
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

    /**
     * Return if the value is true or false as expected.
     *
     * @param  boolean|null $value
     * @param  boolean      $expected
     * @return boolean
     */
    public function is(?bool $value, bool $expected=true): bool
    {
        return $value === $expected;
    }

    /**
     * Return if the value is false.
     *
     * @param  boolean|null $value
     * @return boolean
     */
    public function isNot(?bool $value): bool
    {
        return $this->is($value, false);
    }
}
