<?php
/**
 * Define a char field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

class Char extends Text
{
    /**
     * Max length.
     *
     * @var integer
     */
    protected $maxLength;

    /**
     * Define the max length for this field.
     *
     * @param integer $maxLength
     *
     * @return self
     */
    public function maxLength(int $maxLength)
    {
        $this->needsToBeUnlocked();

        if ($maxLength <= 0) {
            throw new \Exception('The max length must be a positive number');
        }

        $this->defineProperty('maxLength', $maxLength);

        return $this;
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
