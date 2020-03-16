<?php
/**
 * Define a timestamp field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

class Timestamp extends DateTime
{
    /**
     * Serialize the value for outputs.
     *
     * @param  mixed $value
     * @return mixed
     */
    public function serialize($value)
    {
        $format = $this->getFormat();

        if ($format === 'timestamp') {
            return $value->getTimestamp();
        }

        return $value->format($format);
    }
}
