<?php
/**
 * Define a pattern field.
 *
 * @author Samy Nastuzzi <samy@nastuzzi.fr>
 *
 * @copyright Copyright (c) 2019
 * @license MIT
 */

namespace Laramore\Fields;

abstract class Pattern extends Char
{
    abstract public function getPattern(): string;

    public function getFlags()
    {
        return null;
    }
}
