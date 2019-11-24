<?php
/**
 * Define a text field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

use Illuminate\Support\Facades\Schema;

class Text extends Field
{
    public function dry($value)
    {
        return $this->transform($value);
    }

    public function cast($value)
    {
        return $this->transform($value);
    }

    public function transform($value)
    {
        return is_null($value) ? $value : (string) $value;
    }

    public function serialize($value)
    {
        return $value;
    }
}
