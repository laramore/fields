<?php
/**
 * Define a float field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2020
 * @license MIT
 */

namespace Laramore\Fields;

use Laramore\Traits\Field\NumberInteraction;

class Decimal extends BaseAttribute
{
    use NumberInteraction;

    protected $leftPrecision;
    protected $rightPrecision;

    /**
     * Define the precision of this float field.
     *
     * @param int $left
     * @param int $right
     * @return void
     */
    public function precision(int $left=null, int $right=null)
    {
        $this->needsToBeUnlocked();

        if (\func_num_args() === 1) {
            $this->rightPrecision = $left;
        } else {
            $this->leftPrecision = $left;
            $this->rightPrecision = $right;
        }

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
        return is_null($value) ? $value : (float) $value;
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
